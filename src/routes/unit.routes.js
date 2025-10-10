const express = require('express');
const router = express.Router();
const unitController = require('../controllers/unit.controller');
const { authenticate, authorize } = require('../middleware/auth.middleware');

// Public route - anyone can see available units
router.get('/', unitController.listUnits);
router.get('/:id', unitController.getUnitById);

// Protected routes - require authentication and admin role
router.use(authenticate);
router.post('/', authorize('ADMIN'), unitController.createUnit);
router.put('/:id', authorize('ADMIN'), unitController.updateUnit);
router.delete('/:id', authorize('ADMIN'), unitController.deleteUnit);

module.exports = router;
