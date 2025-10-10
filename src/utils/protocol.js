/**
 * Generate unique protocol for requests
 * Format: ESIC-YYYY-NNNNNN
 */
const generateProtocol = () => {
  const year = new Date().getFullYear();
  const random = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
  return `ESIC-${year}-${random}`;
};

/**
 * Calculate deadline (20 days as per LAI)
 */
const calculateDeadline = (startDate = new Date()) => {
  const deadline = new Date(startDate);
  deadline.setDate(deadline.getDate() + 20);
  return deadline;
};

/**
 * Check if deadline is approaching (less than 5 days)
 */
const isDeadlineApproaching = (deadline) => {
  const now = new Date();
  const daysRemaining = Math.ceil((deadline - now) / (1000 * 60 * 60 * 24));
  return daysRemaining <= 5 && daysRemaining > 0;
};

/**
 * Check if deadline has passed
 */
const isDeadlinePassed = (deadline) => {
  return new Date() > deadline;
};

module.exports = {
  generateProtocol,
  calculateDeadline,
  isDeadlineApproaching,
  isDeadlinePassed,
};
