const express = require('express');
const router = express.Router();
const appealController = require('../controllers/appeal.controller');
const { authenticate, authorize } = require('../middleware/auth.middleware');

// All routes require authentication
router.use(authenticate);

// Citizen routes
router.post('/request/:protocol', appealController.createAppeal);

// View appeals
router.get('/request/:protocol', appealController.listRequestAppeals);
router.get('/:id', appealController.getAppealById);

// Staff routes
router.put('/:id/status', authorize('AGENT', 'MANAGER', 'ADMIN'), appealController.updateAppealStatus);

module.exports = router;
