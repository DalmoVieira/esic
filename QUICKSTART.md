# 🚀 Quick Start Guide - E-SIC

Get the E-SIC system running in 5 minutes!

## Prerequisites

- Node.js 18+ installed
- PostgreSQL 14+ installed and running
- Git installed

## 🎯 Option 1: Quick Setup with Script (Recommended)

```bash
# 1. Clone the repository
git clone https://github.com/DalmoVieira/esic.git
cd esic

# 2. Run the setup script
./setup.sh

# 3. Configure your database
cp .env.example .env
nano .env  # Edit with your database credentials

# 4. Initialize the database
npm run prisma:migrate
npm run prisma:seed

# 5. Start the server
npm run dev
```

Visit: http://localhost:3001

## 🐳 Option 2: Docker (Easiest)

```bash
# 1. Clone the repository
git clone https://github.com/DalmoVieira/esic.git
cd esic

# 2. Configure environment (optional)
cp .env.docker .env

# 3. Start everything with Docker Compose
docker-compose up -d

# 4. Wait for initialization (about 30 seconds)

# 5. Check if it's running
curl http://localhost:3001/api/health
```

The system is now running with PostgreSQL included!

## 📝 Test it Out

### 1. Access the landing page
```
http://localhost:3001
```

### 2. Test the API
```bash
# Health check
curl http://localhost:3001/api/health

# List available units
curl http://localhost:3001/api/units

# Login as citizen
curl -X POST http://localhost:3001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "joao.cidadao@email.com",
    "password": "Citizen123"
  }'
```

### 3. Use Postman
1. Import `E-SIC.postman_collection.json`
2. Set `baseUrl` to `http://localhost:3001/api`
3. Login and get a token
4. Set the `token` variable
5. Test all endpoints!

## 👥 Test Accounts

The seed data creates these accounts:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@esic.gov.br | Admin123 |
| Manager | gestor.educacao@cidade.gov.br | Manager123 |
| Agent | agente.educacao@cidade.gov.br | Agent123 |
| Citizen | joao.cidadao@email.com | Citizen123 |

## 🔧 Troubleshooting

### Database connection error?
```bash
# Check if PostgreSQL is running
sudo systemctl status postgresql

# Or start it
sudo systemctl start postgresql

# Verify connection
psql -U postgres -c "SELECT version();"
```

### Port 3001 already in use?
```bash
# Change port in .env
PORT=3002

# Or find and kill the process
lsof -ti:3001 | xargs kill -9
```

### Prisma Client not generated?
```bash
npm run prisma:generate
```

### Need to reset database?
```bash
# Drop all tables and recreate
npx prisma migrate reset

# Re-seed
npm run prisma:seed
```

## 📚 Next Steps

1. **Read the documentation:**
   - [README.md](README.md) - Overview and installation
   - [DOCUMENTATION.md](DOCUMENTATION.md) - API reference
   - [TESTING.md](TESTING.md) - Testing guide

2. **Test the API:**
   - Use Postman collection
   - Try cURL examples from TESTING.md
   - Create your own requests

3. **Customize:**
   - Add your government units
   - Create user accounts
   - Configure email notifications (if needed)

4. **Deploy:**
   - Use Docker for production
   - Set up reverse proxy (nginx)
   - Configure SSL certificate
   - Set strong JWT_SECRET

## 🎓 Learn More

- **API Endpoints:** See DOCUMENTATION.md for all 30+ endpoints
- **Database Schema:** Check `prisma/schema.prisma`
- **Contributing:** Read CONTRIBUTING.md
- **Testing:** Follow TESTING.md guide

## ⚡ Quick Commands

```bash
# Development
npm run dev              # Start with hot reload
npm run prisma:studio    # Open database GUI

# Database
npm run prisma:generate  # Generate Prisma Client
npm run prisma:migrate   # Run migrations
npm run prisma:seed      # Seed sample data

# Production
npm start                # Start production server

# Docker
docker-compose up -d     # Start containers
docker-compose down      # Stop containers
docker-compose logs -f   # View logs
```

## 💡 Pro Tips

1. **Use Prisma Studio** for visual database management:
   ```bash
   npm run prisma:studio
   ```
   Opens at: http://localhost:5555

2. **Enable query logging** for debugging (in development):
   Check the logs in the terminal when running `npm run dev`

3. **Test with real scenarios:**
   - Create a request as citizen
   - Login as agent to respond
   - Create an appeal
   - Test different roles

4. **Check the health endpoint** regularly:
   ```bash
   curl http://localhost:3001/api/health
   ```

## 🎯 Common Tasks

### Create a new citizen
```bash
curl -X POST http://localhost:3001/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "novo@email.com",
    "password": "Senha123",
    "name": "Novo Usuário"
  }'
```

### Create an information request
```bash
# First, login and get token
TOKEN="your-token-here"
UNIT_ID="unit-id-here"

curl -X POST http://localhost:3001/api/requests \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"subject\": \"Minha solicitação\",
    \"description\": \"Descrição detalhada...\",
    \"unitId\": \"$UNIT_ID\"
  }"
```

### Check a request by protocol
```bash
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:3001/api/requests/protocol/ESIC-2025-000001
```

## 🎉 You're Ready!

The E-SIC system is now running and ready to use. Start testing the API and explore all features!

Need help? Check the documentation or open an issue on GitHub.

Happy coding! 🏛️✨
