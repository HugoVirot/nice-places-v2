import MonCompte from './MonCompte.vue'

beforeEach(() => {
  cy.session({
      preserve: ['XSRF-TOKEN', 'session_id']
  })
})

describe('<MonCompte />', () => {
  it('renders', () => {
    // see: https://test-utils.vuejs.org/guide/
    cy.mount(MonCompte)
  })
})