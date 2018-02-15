  <div class="user">
        <img src="/csm/public/images/logo.png" alt="Esempio" class="img-thumbnail"><br>
    </div>

    <div class="list-group">

    <a href="#" id="vendorUPC" class="list-group-item">UPC Price Compare</a>
      <form method = 'POST' action = '/csm/public/home/UPCPriceCompare' name='upcform' id = 'upcform'>
        <input type='hidden' name = 'upcNumber' id = 'upcNumber'>
        <input type='hidden' name = 'fromupc' id = 'fromupc'>
        <input type='hidden' name = 'toupc' id = 'toupc'>
      </form>

  <a href="#" id="vendoritemcode" class="list-group-item">Vendor Item Code</a>
    <form method = 'POST' action = '/csm/public/home/vendorItemCode' name='itemcodeform' id = 'itemcodeform'>
        <input type='hidden' name = 'itemcode' id = 'itemcode'>
        <input type='hidden' name = 'fromcode' id = 'fromcode'>
        <input type='hidden' name = 'tocode' id = 'tocode'>
      </form>

  <a href="#" id="receivingUPC" class="list-group-item">UPC Receiving History</a>
    <form method = 'POST' action = '/csm/public/home/UPCReceivingHistory' name='upcReceivingform' id = 'upcReceivingform'>
      <input type='hidden' name = 'upcReceivingNumber' id = 'upcReceivingNumber'>
      <input type='hidden' name = 'fromReceivingupc' id = 'fromReceivingupc'>
      <input type='hidden' name = 'toReceivingupc' id = 'toReceivingupc'>
    </form>

    <a href="#" id="description" class="list-group-item">Item Description</a>
      <form method = 'POST' action = '/csm/public/home/itemDescription' name='descriptionform' id = 'descriptionform'>
          <input type = 'hidden' name = 'itemDescription' id='itemDescription'>
          <input type='hidden' name = 'descriptionfrom' id = 'fromdescription'>
          <input type='hidden' name = 'descriptionto' id = 'todescription'>
      </form>

  <a href="#" id="vendor" class="list-group-item">Vendor Complete</a>
      <form method = 'POST' action = '/csm/public/home/vendor' name='vendorform' id = 'vendorform'>
          <input type='hidden' name = 'vendorNumber' id = 'vendorNumber'>
          <input type='hidden' name = 'fromvendor' id = 'fromvendor'>
          <input type='hidden' name = 'tovendor' id = 'tovendor'>
        </form>

    <a href="#" id="vendorNegative" class="list-group-item">Vendor Complete - Negative</a>
      <form method = 'POST' action = '/csm/public/home/vendorNegative' name='vendorNegativeform' id = 'vendorNegativeform'>
          <input type='hidden' name = 'vendorNegNumber' id = 'vendorNegNumber'>
          <input type='hidden' name = 'fromNegvendor' id = 'fromNegvendor'>
          <input type='hidden' name = 'toNegvendor' id = 'toNegvendor'>
        </form>
  </div>