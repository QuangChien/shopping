/**
 * Alert management service for the application
 * Singleton pattern to allow access from anywhere without depending on the DOM
 */

// Queue for alerts that need to be displayed
let alertQueue = [];

// Callback to handle new alerts
let alertCallback = null;

// Incrementing ID for alerts
let nextId = 1;

const AlertService = {
    /**
     * Register a callback to handle alerts
     * @param {Function} callback 
     */
    register(callback) {
        alertCallback = callback;
        
        // Flush any queued alerts
        if (alertQueue.length > 0) {
            const pending = [...alertQueue];
            alertQueue = [];
            pending.forEach(alert => this.addAlert(alert.type, alert.message, alert.options));
        }
    },
    
    /**
     * Unregister the callback
     */
    unregister() {
        alertCallback = null;
    },
    
    /**
     * Add a new alert to the system
     * @param {String} type Type of alert: success, error, warning, info
     * @param {String} message Alert content
     * @param {Object} options Additional options
     */
    addAlert(type, message, options = {}) {
        const alert = {
            id: `alert-${nextId++}`,
            type,
            message,
            options
        };
        
        if (alertCallback) {
            alertCallback(alert);
        } else {
            // If no callback is registered yet, store in the queue
            alertQueue.push(alert);
        }
        
        return alert.id;
    },
    
    /**
     * Shorthand methods
     */
    success(message, options = {}) {
        return this.addAlert('success', message, options);
    },
    
    error(message, options = {}) {
        return this.addAlert('error', message, options);
    },
    
    warning(message, options = {}) {
        return this.addAlert('warning', message, options);
    },
    
    info(message, options = {}) {
        return this.addAlert('info', message, options);
    }
};

export default AlertService;
