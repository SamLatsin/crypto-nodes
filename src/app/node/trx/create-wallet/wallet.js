async function gen(mnemonic = null, private_key = null) {
	const hdWallet = require("tron-wallet-hd");
	const utils=hdWallet.utils;
	if (!mnemonic) {
		mnemonic = utils.generateMnemonic();
	}
	if (!private_key) {
		account = await utils.getAccountAtIndex(mnemonic,1);
		private_key = account.privateKey;
		address = account.address;
	}
	else {
		address = await utils.getAccountFromPrivateKey(private_key);
	}
	const res = {
      "mnemonic": mnemonic,
      "privateKey": private_key,
      "address": address
    };
	console.log(JSON.stringify(res)); 
}
const args = process.argv.slice(2)
gen(args[0], args[1]);
