/**
 * Centralized API fetch utility for Olobuild REST endpoints.
 * Eliminates duplicated fetch + nonce + base URL pattern across 12+ files.
 *
 * Usage:
 *   import { oloFetch } from '@/composables/useApi';
 *   const data = await oloFetch('/templates');
 *   const data = await oloFetch('/templates', { method: 'POST', body: { name: 'Test' } });
 */

function getOloData() {
  return window.oloData || {};
}

/**
 * Fetch from the Olobuild REST API with automatic base URL and nonce.
 *
 * @param {string} endpoint  - REST path relative to namespace (e.g. '/templates', '/styles')
 * @param {Object} [options] - Fetch options
 * @param {string} [options.method='GET'] - HTTP method
 * @param {Object} [options.body] - Request body (will be JSON-stringified)
 * @param {Object} [options.params] - URL query params for GET requests
 * @param {Object} [options.headers] - Additional headers
 * @returns {Promise<any>} Parsed JSON response
 */
export async function oloFetch(endpoint, options = {}) {
  const oloData = getOloData();
  const baseUrl = (oloData.restUrl || '/wp-json/').replace(/\/$/, '');
  const namespace = 'olo/v1';

  let url = `${baseUrl}/${namespace}${endpoint}`;

  const method = (options.method || 'GET').toUpperCase();

  // Append query params for GET
  if (options.params && method === 'GET') {
    const qs = new URLSearchParams(options.params).toString();
    url += (url.includes('?') ? '&' : '?') + qs;
  }

  const fetchOpts = {
    method,
    headers: {
      'X-WP-Nonce': oloData.nonce || '',
      ...(options.headers || {}),
    },
    credentials: 'same-origin',
  };

  if (options.body && method !== 'GET') {
    if (options.body instanceof FormData) {
      fetchOpts.body = options.body;
      // Don't set Content-Type for FormData (browser sets multipart boundary)
    } else {
      fetchOpts.headers['Content-Type'] = 'application/json';
      fetchOpts.body = JSON.stringify(options.body);
    }
  }

  const response = await fetch(url, fetchOpts);

  if (!response.ok) {
    const errorBody = await response.json().catch(() => ({}));
    const error = new Error(errorBody.message || `API error ${response.status}`);
    error.status = response.status;
    error.data = errorBody;
    throw error;
  }

  // Handle 204 No Content
  if (response.status === 204) return null;

  return response.json();
}

/**
 * Composable wrapper for Vue components.
 * Returns the same oloFetch function for use with inject/provide pattern.
 */
export function useApi() {
  return { oloFetch };
}
