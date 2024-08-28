import Account from './Account.vue'

beforeEach(() => {
  cy.session({
      preserve: ['XSRF-TOKEN', 'session_id']
  })
})

describe('<Account />', () => {
  it('renders', () => {
    // see: https://test-utils.vuejs.org/guide/
    cy.mount(Account)
  })
})