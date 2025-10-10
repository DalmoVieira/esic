const { PrismaClient } = require('@prisma/client');
const bcrypt = require('bcryptjs');

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Starting database seed...');

  // Create Units
  console.log('Creating units...');
  const units = await Promise.all([
    prisma.unit.create({
      data: {
        name: 'Secretaria de Educação',
        description: 'Responsável pela gestão educacional do município',
        email: 'educacao@cidade.gov.br',
        phone: '1133334444',
        address: 'Rua da Educação, 100',
      },
    }),
    prisma.unit.create({
      data: {
        name: 'Secretaria de Saúde',
        description: 'Responsável pela gestão da saúde pública',
        email: 'saude@cidade.gov.br',
        phone: '1133335555',
        address: 'Avenida da Saúde, 200',
      },
    }),
    prisma.unit.create({
      data: {
        name: 'Secretaria de Obras',
        description: 'Responsável por obras e infraestrutura',
        email: 'obras@cidade.gov.br',
        phone: '1133336666',
        address: 'Rua das Obras, 300',
      },
    }),
  ]);
  console.log(`✅ Created ${units.length} units`);

  // Create Admin user
  console.log('Creating admin user...');
  const adminPassword = await bcrypt.hash('Admin123', 10);
  const admin = await prisma.user.create({
    data: {
      email: 'admin@esic.gov.br',
      password: adminPassword,
      name: 'Administrador do Sistema',
      role: 'ADMIN',
      active: true,
    },
  });
  console.log('✅ Created admin user (email: admin@esic.gov.br, password: Admin123)');

  // Create Manager for Education unit
  console.log('Creating manager...');
  const managerPassword = await bcrypt.hash('Manager123', 10);
  const manager = await prisma.user.create({
    data: {
      email: 'gestor.educacao@cidade.gov.br',
      password: managerPassword,
      name: 'Maria Silva',
      role: 'MANAGER',
      unitId: units[0].id,
      active: true,
    },
  });
  console.log('✅ Created manager (email: gestor.educacao@cidade.gov.br, password: Manager123)');

  // Create Agents
  console.log('Creating agents...');
  const agentPassword = await bcrypt.hash('Agent123', 10);
  const agents = await Promise.all([
    prisma.user.create({
      data: {
        email: 'agente.educacao@cidade.gov.br',
        password: agentPassword,
        name: 'João Santos',
        role: 'AGENT',
        unitId: units[0].id,
        active: true,
      },
    }),
    prisma.user.create({
      data: {
        email: 'agente.saude@cidade.gov.br',
        password: agentPassword,
        name: 'Ana Costa',
        role: 'AGENT',
        unitId: units[1].id,
        active: true,
      },
    }),
  ]);
  console.log(`✅ Created ${agents.length} agents (password: Agent123)`);

  // Create Citizens
  console.log('Creating citizens...');
  const citizenPassword = await bcrypt.hash('Citizen123', 10);
  const citizens = await Promise.all([
    prisma.user.create({
      data: {
        email: 'joao.cidadao@email.com',
        password: citizenPassword,
        name: 'João Cidadão',
        cpf: '12345678901',
        phone: '11987654321',
        role: 'CITIZEN',
        active: true,
      },
    }),
    prisma.user.create({
      data: {
        email: 'maria.cidadao@email.com',
        password: citizenPassword,
        name: 'Maria Cidadã',
        cpf: '98765432109',
        phone: '11912345678',
        role: 'CITIZEN',
        active: true,
      },
    }),
  ]);
  console.log(`✅ Created ${citizens.length} citizens (password: Citizen123)`);

  // Create sample requests
  console.log('Creating sample requests...');
  const deadlineDate = new Date();
  deadlineDate.setDate(deadlineDate.getDate() + 20);

  const request1 = await prisma.request.create({
    data: {
      protocol: 'ESIC-2025-000001',
      subject: 'Informações sobre merenda escolar',
      description: 'Solicito informações detalhadas sobre os gastos com merenda escolar no último trimestre.',
      citizenId: citizens[0].id,
      unitId: units[0].id,
      status: 'PENDING',
      deadlineAt: deadlineDate,
    },
  });

  await prisma.timeline.create({
    data: {
      requestId: request1.id,
      action: 'Solicitação criada',
      description: 'Protocolo ESIC-2025-000001 gerado',
    },
  });

  const request2 = await prisma.request.create({
    data: {
      protocol: 'ESIC-2025-000002',
      subject: 'Dados de vacinação',
      description: 'Gostaria de obter dados sobre a campanha de vacinação do município.',
      citizenId: citizens[1].id,
      unitId: units[1].id,
      status: 'IN_PROGRESS',
      deadlineAt: deadlineDate,
    },
  });

  await prisma.timeline.create({
    data: {
      requestId: request2.id,
      action: 'Solicitação criada',
      description: 'Protocolo ESIC-2025-000002 gerado',
    },
  });

  // Add a response to request2
  await prisma.response.create({
    data: {
      content: 'Estamos compilando os dados solicitados. Em breve enviaremos a resposta completa.',
      partial: true,
      requestId: request2.id,
      authorId: agents[1].id,
    },
  });

  await prisma.timeline.create({
    data: {
      requestId: request2.id,
      action: 'Resposta parcial adicionada',
      description: 'Por Ana Costa',
    },
  });

  console.log('✅ Created 2 sample requests');

  console.log('');
  console.log('🎉 Database seeded successfully!');
  console.log('');
  console.log('📝 Test accounts created:');
  console.log('');
  console.log('Admin:');
  console.log('  Email: admin@esic.gov.br');
  console.log('  Password: Admin123');
  console.log('');
  console.log('Manager:');
  console.log('  Email: gestor.educacao@cidade.gov.br');
  console.log('  Password: Manager123');
  console.log('');
  console.log('Agent:');
  console.log('  Email: agente.educacao@cidade.gov.br');
  console.log('  Password: Agent123');
  console.log('');
  console.log('Citizen:');
  console.log('  Email: joao.cidadao@email.com');
  console.log('  Password: Citizen123');
  console.log('');
}

main()
  .catch((e) => {
    console.error('Error seeding database:', e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
