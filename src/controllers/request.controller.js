const prisma = require('../config/database');
const { generateProtocol, calculateDeadline } = require('../utils/protocol');

/**
 * Create new information request
 */
const createRequest = async (req, res, next) => {
  try {
    const { subject, description, unitId, anonymous } = req.body;
    const citizenId = req.user.id;

    // Validate required fields
    if (!subject || !description || !unitId) {
      return res.status(400).json({ error: 'Assunto, descrição e unidade são obrigatórios' });
    }

    // Verify unit exists
    const unit = await prisma.unit.findUnique({ where: { id: unitId } });
    if (!unit) {
      return res.status(404).json({ error: 'Unidade não encontrada' });
    }

    // Generate protocol
    let protocol;
    let protocolExists = true;
    while (protocolExists) {
      protocol = generateProtocol();
      const existing = await prisma.request.findUnique({ where: { protocol } });
      protocolExists = !!existing;
    }

    // Calculate deadline (20 days as per LAI)
    const deadlineAt = calculateDeadline();

    // Create request
    const request = await prisma.request.create({
      data: {
        protocol,
        subject,
        description,
        citizenId,
        unitId,
        anonymous: anonymous || false,
        deadlineAt,
        status: 'PENDING',
      },
      include: {
        unit: {
          select: {
            id: true,
            name: true,
          },
        },
        citizen: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
      },
    });

    // Create timeline entry
    await prisma.timeline.create({
      data: {
        requestId: request.id,
        action: 'Solicitação criada',
        description: `Protocolo ${protocol} gerado`,
      },
    });

    res.status(201).json({
      message: 'Solicitação criada com sucesso',
      request,
    });
  } catch (error) {
    next(error);
  }
};

/**
 * Get request by protocol
 */
const getRequestByProtocol = async (req, res, next) => {
  try {
    const { protocol } = req.params;

    const request = await prisma.request.findUnique({
      where: { protocol },
      include: {
        unit: {
          select: {
            id: true,
            name: true,
            email: true,
            phone: true,
          },
        },
        citizen: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
        responses: {
          include: {
            author: {
              select: {
                id: true,
                name: true,
                role: true,
              },
            },
            documents: true,
          },
          orderBy: {
            createdAt: 'desc',
          },
        },
        documents: true,
        appeals: {
          include: {
            documents: true,
          },
          orderBy: {
            createdAt: 'desc',
          },
        },
        timeline: {
          orderBy: {
            createdAt: 'asc',
          },
        },
      },
    });

    if (!request) {
      return res.status(404).json({ error: 'Solicitação não encontrada' });
    }

    // Check authorization
    const isOwner = request.citizenId === req.user.id;
    const isStaff = ['AGENT', 'MANAGER', 'ADMIN'].includes(req.user.role);
    const isUnitStaff = isStaff && request.unitId === req.user.unitId;

    if (!isOwner && !isUnitStaff && req.user.role !== 'ADMIN') {
      return res.status(403).json({ error: 'Acesso negado' });
    }

    res.json(request);
  } catch (error) {
    next(error);
  }
};

/**
 * List user's requests
 */
const listMyRequests = async (req, res, next) => {
  try {
    const { status, page = 1, limit = 10 } = req.query;
    const skip = (page - 1) * limit;

    const where = { citizenId: req.user.id };
    if (status) {
      where.status = status;
    }

    const [requests, total] = await Promise.all([
      prisma.request.findMany({
        where,
        include: {
          unit: {
            select: {
              id: true,
              name: true,
            },
          },
          responses: {
            select: {
              id: true,
              createdAt: true,
            },
          },
        },
        orderBy: {
          createdAt: 'desc',
        },
        skip: parseInt(skip),
        take: parseInt(limit),
      }),
      prisma.request.count({ where }),
    ]);

    res.json({
      requests,
      pagination: {
        page: parseInt(page),
        limit: parseInt(limit),
        total,
        pages: Math.ceil(total / limit),
      },
    });
  } catch (error) {
    next(error);
  }
};

/**
 * List requests for unit staff
 */
const listUnitRequests = async (req, res, next) => {
  try {
    const { status, page = 1, limit = 10 } = req.query;
    const skip = (page - 1) * limit;

    // Check if user is staff
    if (!['AGENT', 'MANAGER', 'ADMIN'].includes(req.user.role)) {
      return res.status(403).json({ error: 'Acesso negado' });
    }

    const where = {};
    
    // Filter by unit for agents and managers
    if (req.user.role !== 'ADMIN') {
      where.unitId = req.user.unitId;
    }

    if (status) {
      where.status = status;
    }

    const [requests, total] = await Promise.all([
      prisma.request.findMany({
        where,
        include: {
          unit: {
            select: {
              id: true,
              name: true,
            },
          },
          citizen: {
            select: {
              id: true,
              name: true,
              email: true,
            },
          },
          responses: {
            select: {
              id: true,
              createdAt: true,
            },
          },
        },
        orderBy: {
          createdAt: 'desc',
        },
        skip: parseInt(skip),
        take: parseInt(limit),
      }),
      prisma.request.count({ where }),
    ]);

    res.json({
      requests,
      pagination: {
        page: parseInt(page),
        limit: parseInt(limit),
        total,
        pages: Math.ceil(total / limit),
      },
    });
  } catch (error) {
    next(error);
  }
};

/**
 * Add response to request
 */
const addResponse = async (req, res, next) => {
  try {
    const { protocol } = req.params;
    const { content, partial } = req.body;
    const authorId = req.user.id;

    if (!content) {
      return res.status(400).json({ error: 'Conteúdo da resposta é obrigatório' });
    }

    // Find request
    const request = await prisma.request.findUnique({
      where: { protocol },
    });

    if (!request) {
      return res.status(404).json({ error: 'Solicitação não encontrada' });
    }

    // Check authorization
    const isStaff = ['AGENT', 'MANAGER', 'ADMIN'].includes(req.user.role);
    const isUnitStaff = isStaff && request.unitId === req.user.unitId;

    if (!isUnitStaff && req.user.role !== 'ADMIN') {
      return res.status(403).json({ error: 'Acesso negado' });
    }

    // Create response
    const response = await prisma.response.create({
      data: {
        content,
        partial: partial || false,
        requestId: request.id,
        authorId,
      },
      include: {
        author: {
          select: {
            id: true,
            name: true,
            role: true,
          },
        },
      },
    });

    // Update request status
    const newStatus = partial ? 'IN_PROGRESS' : 'ANSWERED';
    await prisma.request.update({
      where: { id: request.id },
      data: { status: newStatus },
    });

    // Create timeline entry
    await prisma.timeline.create({
      data: {
        requestId: request.id,
        action: partial ? 'Resposta parcial adicionada' : 'Solicitação respondida',
        description: `Por ${req.user.name}`,
      },
    });

    res.status(201).json({
      message: 'Resposta adicionada com sucesso',
      response,
    });
  } catch (error) {
    next(error);
  }
};

/**
 * Update request status
 */
const updateRequestStatus = async (req, res, next) => {
  try {
    const { protocol } = req.params;
    const { status } = req.body;

    const validStatuses = ['PENDING', 'IN_PROGRESS', 'ANSWERED', 'DENIED', 'APPEALED', 'CLOSED'];
    if (!validStatuses.includes(status)) {
      return res.status(400).json({ error: 'Status inválido' });
    }

    // Find request
    const request = await prisma.request.findUnique({
      where: { protocol },
    });

    if (!request) {
      return res.status(404).json({ error: 'Solicitação não encontrada' });
    }

    // Check authorization
    const isStaff = ['AGENT', 'MANAGER', 'ADMIN'].includes(req.user.role);
    const isUnitStaff = isStaff && request.unitId === req.user.unitId;

    if (!isUnitStaff && req.user.role !== 'ADMIN') {
      return res.status(403).json({ error: 'Acesso negado' });
    }

    // Update status
    const updatedRequest = await prisma.request.update({
      where: { id: request.id },
      data: { status },
    });

    // Create timeline entry
    await prisma.timeline.create({
      data: {
        requestId: request.id,
        action: `Status alterado para ${status}`,
        description: `Por ${req.user.name}`,
      },
    });

    res.json({
      message: 'Status atualizado com sucesso',
      request: updatedRequest,
    });
  } catch (error) {
    next(error);
  }
};

module.exports = {
  createRequest,
  getRequestByProtocol,
  listMyRequests,
  listUnitRequests,
  addResponse,
  updateRequestStatus,
};
