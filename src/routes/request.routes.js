const express = require('express');
const router = express.Router();
const requestController = require('../controllers/request.controller');
const { authenticate, authorize } = require('../middleware/auth.middleware');

// All routes require authentication
router.use(authenticate);

// Citizen routes
router.post('/', requestController.createRequest);
router.get('/my-requests', requestController.listMyRequests);
router.get('/protocol/:protocol', requestController.getRequestByProtocol);

// Staff routes
router.get('/unit', authorize('AGENT', 'MANAGER', 'ADMIN'), requestController.listUnitRequests);
router.post('/protocol/:protocol/response', authorize('AGENT', 'MANAGER', 'ADMIN'), requestController.addResponse);
router.put('/protocol/:protocol/status', authorize('AGENT', 'MANAGER', 'ADMIN'), requestController.updateRequestStatus);

module.exports = router;
