const prisma = require('../config/database');
const { hashPassword, validatePassword } = require('../utils/password');
const { validateCPF, validateEmail } = require('../utils/validators');

/**
 * List all users (Admin only)
 */
const listUsers = async (req, res, next) => {
  try {
    const { role, active, page = 1, limit = 10 } = req.query;
    const skip = (page - 1) * limit;

    const where = {};
    if (role) where.role = role;
    if (active !== undefined) where.active = active === 'true';

    const [users, total] = await Promise.all([
      prisma.user.findMany({
        where,
        select: {
          id: true,
          email: true,
          name: true,
          cpf: true,
          phone: true,
          role: true,
          active: true,
          createdAt: true,
          unit: {
            select: {
              id: true,
              name: true,
            },
          },
        },
        orderBy: {
          createdAt: 'desc',
        },
        skip: parseInt(skip),
        take: parseInt(limit),
      }),
      prisma.user.count({ where }),
    ]);

    res.json({
      users,
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
 * Get user by ID
 */
const getUserById = async (req, res, next) => {
  try {
    const { id } = req.params;

    const user = await prisma.user.findUnique({
      where: { id },
      select: {
        id: true,
        email: true,
        name: true,
        cpf: true,
        phone: true,
        role: true,
        active: true,
        createdAt: true,
        updatedAt: true,
        unit: {
          select: {
            id: true,
            name: true,
          },
        },
      },
    });

    if (!user) {
      return res.status(404).json({ error: 'Usuário não encontrado' });
    }

    res.json(user);
  } catch (error) {
    next(error);
  }
};

/**
 * Create new user (Admin/Manager only)
 */
const createUser = async (req, res, next) => {
  try {
    const { email, password, name, cpf, phone, role, unitId } = req.body;

    // Validate required fields
    if (!email || !password || !name || !role) {
      return res.status(400).json({ error: 'Email, senha, nome e role são obrigatórios' });
    }

    // Validate email
    if (!validateEmail(email)) {
      return res.status(400).json({ error: 'Email inválido' });
    }

    // Validate password
    const passwordValidation = validatePassword(password);
    if (!passwordValidation.valid) {
      return res.status(400).json({ error: passwordValidation.message });
    }

    // Validate CPF if provided
    if (cpf && !validateCPF(cpf)) {
      return res.status(400).json({ error: 'CPF inválido' });
    }

    // Validate role
    const validRoles = ['CITIZEN', 'AGENT', 'MANAGER', 'ADMIN'];
    if (!validRoles.includes(role)) {
      return res.status(400).json({ error: 'Role inválida' });
    }

    // Check if user already exists
    const existingUser = await prisma.user.findUnique({ where: { email } });
    if (existingUser) {
      return res.status(400).json({ error: 'Email já cadastrado' });
    }

    // Hash password
    const hashedPassword = await hashPassword(password);

    // Create user
    const user = await prisma.user.create({
      data: {
        email,
        password: hashedPassword,
        name,
        cpf: cpf || null,
        phone: phone || null,
        role,
        unitId: unitId || null,
      },
      select: {
        id: true,
        email: true,
        name: true,
        role: true,
        createdAt: true,
        unit: {
          select: {
            id: true,
            name: true,
          },
        },
      },
    });

    res.status(201).json({
      message: 'Usuário criado com sucesso',
      user,
    });
  } catch (error) {
    next(error);
  }
};

/**
 * Update user (Admin/Manager only)
 */
const updateUser = async (req, res, next) => {
  try {
    const { id } = req.params;
    const { name, email, phone, role, unitId, active } = req.body;

    // Check if user exists
    const existingUser = await prisma.user.findUnique({ where: { id } });
    if (!existingUser) {
      return res.status(404).json({ error: 'Usuário não encontrado' });
    }

    const updateData = {};
    if (name) updateData.name = name;
    if (phone) updateData.phone = phone;
    if (role) {
      const validRoles = ['CITIZEN', 'AGENT', 'MANAGER', 'ADMIN'];
      if (!validRoles.includes(role)) {
        return res.status(400).json({ error: 'Role inválida' });
      }
      updateData.role = role;
    }
    if (unitId !== undefined) updateData.unitId = unitId;
    if (active !== undefined) updateData.active = active;
    if (email) {
      if (!validateEmail(email)) {
        return res.status(400).json({ error: 'Email inválido' });
      }
      updateData.email = email;
    }

    const user = await prisma.user.update({
      where: { id },
      data: updateData,
      select: {
        id: true,
        email: true,
        name: true,
        phone: true,
        role: true,
        active: true,
        updatedAt: true,
        unit: {
          select: {
            id: true,
            name: true,
          },
        },
      },
    });

    res.json({
      message: 'Usuário atualizado com sucesso',
      user,
    });
  } catch (error) {
    next(error);
  }
};

/**
 * Delete user (Admin only)
 */
const deleteUser = async (req, res, next) => {
  try {
    const { id } = req.params;

    // Check if user exists
    const user = await prisma.user.findUnique({ where: { id } });
    if (!user) {
      return res.status(404).json({ error: 'Usuário não encontrado' });
    }

    // Instead of deleting, deactivate the user
    await prisma.user.update({
      where: { id },
      data: { active: false },
    });

    res.json({ message: 'Usuário desativado com sucesso' });
  } catch (error) {
    next(error);
  }
};

module.exports = {
  listUsers,
  getUserById,
  createUser,
  updateUser,
  deleteUser,
};
