const prisma = require('../config/database');

/**
 * Create appeal for a request
 */
const createAppeal = async (req, res, next) => {
  try {
    const { protocol } = req.params;
    const { reason } = req.body;
    const citizenId = req.user.id;

    if (!reason) {
      return res.status(400).json({ error: 'Motivo do recurso é obrigatório' });
    }

    // Find request
    const request = await prisma.request.findUnique({
      where: { protocol },
    });

    if (!request) {
      return res.status(404).json({ error: 'Solicitação não encontrada' });
    }

    // Check if user is the owner
    if (request.citizenId !== citizenId) {
      return res.status(403).json({ error: 'Acesso negado' });
    }

    // Check if request can be appealed
    if (!['DENIED', 'ANSWERED'].includes(request.status)) {
      return res.status(400).json({
        error: 'Solicitação não pode receber recurso no status atual',
      });
    }

    // Create appeal
    const appeal = await prisma.appeal.create({
      data: {
        reason,
        requestId: request.id,
        citizenId,
        status: 'PENDING',
      },
    });

    // Update request status
    await prisma.request.update({
      where: { id: request.id },
      data: { status: 'APPEALED' },
    });

    // Create timeline entry
    await prisma.timeline.create({
      data: {
        requestId: request.id,
        action: 'Recurso criado',
        description: 'Aguardando análise',
      },
    });

    res.status(201).json({
      message: 'Recurso criado com sucesso',
      appeal,
    });
  } catch (error) {
    next(error);
  }
};

/**
 * List appeals for a request
 */
const listRequestAppeals = async (req, res, next) => {
  try {
    const { protocol } = req.params;

    // Find request
    const request = await prisma.request.findUnique({
      where: { protocol },
      select: { id: true, citizenId: true, unitId: true },
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

    const appeals = await prisma.appeal.findMany({
      where: { requestId: request.id },
      include: {
        citizen: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
        documents: true,
      },
      orderBy: {
        createdAt: 'desc',
      },
    });

    res.json(appeals);
  } catch (error) {
    next(error);
  }
};

/**
 * Update appeal status and decision (Staff only)
 */
const updateAppealStatus = async (req, res, next) => {
  try {
    const { id } = req.params;
    const { status, decision } = req.body;

    if (!status) {
      return res.status(400).json({ error: 'Status é obrigatório' });
    }

    const validStatuses = ['PENDING', 'ACCEPTED', 'REJECTED'];
    if (!validStatuses.includes(status)) {
      return res.status(400).json({ error: 'Status inválido' });
    }

    // Find appeal
    const appeal = await prisma.appeal.findUnique({
      where: { id },
      include: {
        request: {
          select: {
            id: true,
            protocol: true,
            unitId: true,
          },
        },
      },
    });

    if (!appeal) {
      return res.status(404).json({ error: 'Recurso não encontrado' });
    }

    // Check authorization
    const isStaff = ['AGENT', 'MANAGER', 'ADMIN'].includes(req.user.role);
    const isUnitStaff = isStaff && appeal.request.unitId === req.user.unitId;

    if (!isUnitStaff && req.user.role !== 'ADMIN') {
      return res.status(403).json({ error: 'Acesso negado' });
    }

    // Update appeal
    const updatedAppeal = await prisma.appeal.update({
      where: { id },
      data: {
        status,
        decision: decision || null,
        decidedAt: new Date(),
      },
    });

    // Create timeline entry
    await prisma.timeline.create({
      data: {
        requestId: appeal.request.id,
        action: `Recurso ${status === 'ACCEPTED' ? 'aceito' : 'rejeitado'}`,
        description: decision || `Por ${req.user.name}`,
      },
    });

    res.json({
      message: 'Recurso atualizado com sucesso',
      appeal: updatedAppeal,
    });
  } catch (error) {
    next(error);
  }
};

/**
 * Get appeal by ID
 */
const getAppealById = async (req, res, next) => {
  try {
    const { id } = req.params;

    const appeal = await prisma.appeal.findUnique({
      where: { id },
      include: {
        request: {
          select: {
            id: true,
            protocol: true,
            subject: true,
            unitId: true,
            citizenId: true,
          },
        },
        citizen: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
        documents: true,
      },
    });

    if (!appeal) {
      return res.status(404).json({ error: 'Recurso não encontrado' });
    }

    // Check authorization
    const isOwner = appeal.citizenId === req.user.id;
    const isStaff = ['AGENT', 'MANAGER', 'ADMIN'].includes(req.user.role);
    const isUnitStaff = isStaff && appeal.request.unitId === req.user.unitId;

    if (!isOwner && !isUnitStaff && req.user.role !== 'ADMIN') {
      return res.status(403).json({ error: 'Acesso negado' });
    }

    res.json(appeal);
  } catch (error) {
    next(error);
  }
};

module.exports = {
  createAppeal,
  listRequestAppeals,
  updateAppealStatus,
  getAppealById,
};
