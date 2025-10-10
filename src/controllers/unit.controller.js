const prisma = require('../config/database');

/**
 * List all units
 */
const listUnits = async (req, res, next) => {
  try {
    const { active, page = 1, limit = 10 } = req.query;
    const skip = (page - 1) * limit;

    const where = {};
    if (active !== undefined) where.active = active === 'true';

    const [units, total] = await Promise.all([
      prisma.unit.findMany({
        where,
        select: {
          id: true,
          name: true,
          description: true,
          email: true,
          phone: true,
          address: true,
          active: true,
          createdAt: true,
          _count: {
            select: {
              users: true,
              requests: true,
            },
          },
        },
        orderBy: {
          name: 'asc',
        },
        skip: parseInt(skip),
        take: parseInt(limit),
      }),
      prisma.unit.count({ where }),
    ]);

    res.json({
      units,
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
 * Get unit by ID
 */
const getUnitById = async (req, res, next) => {
  try {
    const { id } = req.params;

    const unit = await prisma.unit.findUnique({
      where: { id },
      include: {
        _count: {
          select: {
            users: true,
            requests: true,
          },
        },
      },
    });

    if (!unit) {
      return res.status(404).json({ error: 'Unidade não encontrada' });
    }

    res.json(unit);
  } catch (error) {
    next(error);
  }
};

/**
 * Create new unit (Admin only)
 */
const createUnit = async (req, res, next) => {
  try {
    const { name, description, email, phone, address } = req.body;

    // Validate required fields
    if (!name || !email) {
      return res.status(400).json({ error: 'Nome e email são obrigatórios' });
    }

    const unit = await prisma.unit.create({
      data: {
        name,
        description: description || null,
        email,
        phone: phone || null,
        address: address || null,
      },
    });

    res.status(201).json({
      message: 'Unidade criada com sucesso',
      unit,
    });
  } catch (error) {
    next(error);
  }
};

/**
 * Update unit (Admin only)
 */
const updateUnit = async (req, res, next) => {
  try {
    const { id } = req.params;
    const { name, description, email, phone, address, active } = req.body;

    // Check if unit exists
    const existingUnit = await prisma.unit.findUnique({ where: { id } });
    if (!existingUnit) {
      return res.status(404).json({ error: 'Unidade não encontrada' });
    }

    const updateData = {};
    if (name) updateData.name = name;
    if (description !== undefined) updateData.description = description;
    if (email) updateData.email = email;
    if (phone !== undefined) updateData.phone = phone;
    if (address !== undefined) updateData.address = address;
    if (active !== undefined) updateData.active = active;

    const unit = await prisma.unit.update({
      where: { id },
      data: updateData,
    });

    res.json({
      message: 'Unidade atualizada com sucesso',
      unit,
    });
  } catch (error) {
    next(error);
  }
};

/**
 * Delete unit (Admin only)
 */
const deleteUnit = async (req, res, next) => {
  try {
    const { id } = req.params;

    // Check if unit exists
    const unit = await prisma.unit.findUnique({
      where: { id },
      include: {
        _count: {
          select: {
            requests: true,
            users: true,
          },
        },
      },
    });

    if (!unit) {
      return res.status(404).json({ error: 'Unidade não encontrada' });
    }

    // Check if unit has active requests or users
    if (unit._count.requests > 0 || unit._count.users > 0) {
      return res.status(400).json({
        error: 'Não é possível excluir unidade com solicitações ou usuários associados',
      });
    }

    await prisma.unit.delete({ where: { id } });

    res.json({ message: 'Unidade excluída com sucesso' });
  } catch (error) {
    next(error);
  }
};

module.exports = {
  listUnits,
  getUnitById,
  createUnit,
  updateUnit,
  deleteUnit,
};
