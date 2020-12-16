import './style.css'
import { mapState } from 'vuex'


export default {
  render(h) {
    return (
      <div id="cf-pro-account">
        <ul>
          <li key="public-key">Public Key {this.publicKey}</li>
          <li key="secret-key">Secret Key {this.secretKey}</li>
          <li key="token">Token {this.token}</li>
        </ul>
      </div>
    )
  },
  computed: mapState({
    token: state => state.account.apiKeys.token,
    publicKey: state => state.account.apiKeys.public,
    secretKey: state => state.account.apiKeys.secret,
  }),

}
