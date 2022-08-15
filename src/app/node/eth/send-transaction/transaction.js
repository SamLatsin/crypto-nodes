const Web3 = require('web3')
const ethTx = require('ethereumjs-tx').Transaction
const readline = require('readline');

function rawTxHex(args) {
  args = JSON.parse(args);
  var provider = 'http://localhost:8545';
  var web3 = new Web3(new Web3.providers.HttpProvider(provider))
  web3.transactionConfirmationBlocks = 1;
  // Exclude 0x at the beginning of the private key
  const addressFrom = args.fromAddress;
  const privKey = Buffer.from(args.privateKey.slice(2), 'hex');
  const addressTo = args.toAddress;
  const valueInEther = args.amount;

  web3.eth.getTransactionCount(addressFrom, "pending").then((txnCount) => {
    web3.eth.getGasPrice().then((gasPrice) => {
      var txObject = {
          'nonce': web3.utils.numberToHex(txnCount),
          'to': addressTo,
          'gasPrice': web3.utils.numberToHex(gasPrice),
          'gasLimit': web3.utils.numberToHex(70000),
          'value': web3.utils.numberToHex(web3.utils.toWei(valueInEther.toString(), 'ether')),
          'type': 2,
      };
      if (args.memo) {
        txObject.data = web3.utils.utf8ToHex(args.memo)
      }
      const tx = new ethTx(txObject, { chain: 'mainnet'})
      // const tx = new ethTx(txObject, { chain: 'ropsten'})
      tx.sign(privKey)
      var serializedTx = tx.serialize();
      var rawTxHex = '0x' + serializedTx.toString('hex');
      console.log(rawTxHex);
    })
  })
  .catch(error => { console.log('Error: ', error.message); });
}
const args = process.argv.slice(2)
// console.log(args);
rawTxHex(args[0])