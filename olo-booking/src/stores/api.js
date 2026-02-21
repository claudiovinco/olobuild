/**
 * API helper — wraps fetch with WP REST nonce.
 */
const cfg = window.oloManagerConfig || {};

export const api = {
    baseUrl: cfg.restUrl || '/wp-json/olo-booking/v2',
    nonce: cfg.nonce || '',

    async request(path, options = {}) {
        const url = this.baseUrl + path;
        const headers = {
            'X-WP-Nonce': this.nonce,
            ...options.headers,
        };
        if (options.body && typeof options.body === 'object') {
            headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(options.body);
        }
        const res = await fetch(url, { ...options, headers });
        const data = await res.json();
        if (!res.ok) {
            throw new Error(data.message || 'Errore del server');
        }
        return data;
    },

    get(path) { return this.request(path); },
    post(path, body) { return this.request(path, { method: 'POST', body }); },
    put(path, body) { return this.request(path, { method: 'PUT', body }); },
    patch(path, body) { return this.request(path, { method: 'PATCH', body }); },
    del(path) { return this.request(path, { method: 'DELETE' }); },
};
