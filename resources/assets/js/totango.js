/**
 * Created by Anthony on 4/18/16.
 */

var totango_options = {
    service_id: "SP-xxxx-yy",
// Add user block to identify the user. (no change from
// previous versions). This user will be used to track usage
// events
    user: {

    },
account:{
    id: "102213x" ,
        name: "Anonymous Industries",
        CSM: "Stringer Bell" 		// attribute on the account

},
// Add product: if you would like to register usage events on
// the specific product
product: {
    id: "Photos",
        account_id: "102213x : Photos",// Optional, if not set we will use $account_id__$product_id
        module: "Editor",
    Revision: "5.3", // attribute on the product-account
    "Account Type": "product"
}
};


(function() { var tracker_name=window.totango_options.tracker_name||"totango";window.totango_tmp_stack=[];window[tracker_name]={go:function(){return -1;},setAccountAttributes:function(){},identify:function(){},track:function(t,o,n,a){window.totango_tmp_stack.push({activity:t,module:o,org:n,user:a}); return -1;}}; var e = document.createElement('script'); e.type = 'text/javascript'; e.async = true; e.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'tracker.totango.com/totango3.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(e, s); })();
