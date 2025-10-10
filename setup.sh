#!/bin/bash

# E-SIC Setup Script
# This script helps set up the E-SIC system

echo "🏛️  E-SIC - Setup Script"
echo "=========================="
echo ""

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 18+ first."
    exit 1
fi

echo "✅ Node.js version: $(node --version)"

# Check if PostgreSQL is running
echo ""
echo "📦 Checking PostgreSQL..."

# Create .env if it doesn't exist
if [ ! -f .env ]; then
    echo ""
    echo "📝 Creating .env file..."
    cp .env.example .env
    echo "⚠️  Please edit .env with your database credentials!"
    echo ""
fi

# Install dependencies
echo "📦 Installing dependencies..."
npm install

if [ $? -ne 0 ]; then
    echo "❌ Failed to install dependencies"
    exit 1
fi

echo ""
echo "✅ Dependencies installed successfully"

# Generate Prisma Client
echo ""
echo "🔧 Generating Prisma Client..."
npm run prisma:generate

if [ $? -ne 0 ]; then
    echo "❌ Failed to generate Prisma Client"
    exit 1
fi

echo ""
echo "✅ Prisma Client generated successfully"

echo ""
echo "=========================="
echo "🎉 Setup completed!"
echo ""
echo "Next steps:"
echo "1. Configure your database in .env file"
echo "2. Run: npm run prisma:migrate"
echo "3. Run: npm run dev"
echo ""
echo "Then access: http://localhost:3001"
echo "=========================="
