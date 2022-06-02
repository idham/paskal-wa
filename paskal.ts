//nyontek dari simpleserver
import { create, Client } from '../src/index';
const axios = require('axios').default;
const { default: PQueue } = require("p-queue");
const queue = new PQueue({ concurrency: 5 });
const express = require('express');
const app = express();
app.use(express.json());
const PORT = 8008;

//nyoba woocommerce client
const WooCommerceRestApi = require("@woocommerce/woocommerce-rest-api").default;
const WooCommerce = new WooCommerceRestApi({
	          url: 'https://ilkomers.com', 
	          consumerKey: 'ck_e6f90a34e8cc77bd1d948fdaaf0905135d158fff', 
	          consumerSecret: 'cs_babfaef91a00779d5cedfe148b64613067f877cd', 
	          version: 'wc/v3' 

function katalog(noCust){
WooCommerce.get("products")
 .then((response) => {
	 	response.data.forEach(cetak);
	   //   console.log(response.data.name);

	    })
.catch((error) => {
	          console.log(error.response.data);
             });
}	     
function cetak(item, index, arr) {
	  console.log(arr[index].name+' '+arr[index].images[0].src+' '+arr[index].price+'\n');
}

//URL webhook
const WEBHOOK_ADDRESS = 'https://cengli.store/callback.php'

async function fire(data){
    return await axios.post(WEBHOOK_ADDRESS, data)
}

const wh = event => async (data) => {
    const ts = Date.now();
    return await queue.add(()=>fire({
        ts,
        event,
        data
    }))
}


//client mongodb
var MongoClient = require('mongodb').MongoClient;
var url = "mongodb://localhost:27017/";

async function start(client:Client){
  app.use(client.middleware);
  client.onAnyMessage(message=>{ 
//	console.log(message.type)
        MongoClient.connect(url, function(err, db) {
  		var dbo = db.db("paskaldb");
		var cari = dbo.collection("users").updateOne(
			{noHP:message.from},
			{$set : {noHP:message.from,nama:message.notifyName,tahap:0,reg:false}},
			{upsert:true}, 
			function(err, res){
    		if (err) throw err;
		console.log(message.from);
		cari = res;
    		db.close();
  		});	
		console.log(message);
	 }); 
       });
  app.listen(PORT, function () {
    console.log(`\n• Listening on port ${PORT}!`);
  });
}


create({
    sessionId:'logger',
    multiDevice: true,
    headless: true,
})
  .then(async client => await start(client))
  .catch(e=>{
    console.log('Error',e.message);
  });
