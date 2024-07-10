import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// window.axios.defaults.headers['Accept'] = 'application/json'

axios.defaults.withCredentials = true
axios.defaults.withXSRFToken = true
