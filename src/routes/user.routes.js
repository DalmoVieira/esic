const express = require('express');
const router = express.Router();
const userController = require('../controllers/user.controller');
const { authenticate, authorize } = require('../middleware/auth.middleware');

// All routes require authentication
router.use(authenticate);

// Admin and Manager routes
router.get('/', authorize('ADMIN', 'MANAGER'), userController.listUsers);
router.get('/:id', authorize('ADMIN', 'MANAGER'), userController.getUserById);
router.post('/', authorize('ADMIN', 'MANAGER'), userController.createUser);
router.put('/:id', authorize('ADMIN', 'MANAGER'), userController.updateUser);

// Admin only
router.delete('/:id', authorize('ADMIN'), userController.deleteUser);

module.exports = router;
