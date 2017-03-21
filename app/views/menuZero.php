  <div class="user">
        <img src="/csm/public/images/logo.png" alt="Esempio" class="img-thumbnail"><br>
    </div>

    <div class="list-group">

    <a href="#" id="vendorUPC" class="list-group-item">UPC Price compare</a>
      <form method = 'POST' action = '/csm/public/home/UPCPriceCompare' name='upcform' id = 'upcform'>
        <input type='hidden' name = 'upcNumber' id = 'upcNumber'>
        <input type='hidden' name = 'fromupc' id = 'fromupc'>
        <input type='hidden' name = 'toupc' id = 'toupc'>
      </form>

  <a href="#" id="vendoritemcode" class="list-group-item">Vendor item code</a>
      <form method = 'POST' action = '/csm/public/home/vendorItemCode' name='itemcodeform' id = 'itemcodeform'>
          <input type='hidden' name = 'itemcode' id = 'itemcode'>
          <input type='hidden' name = 'fromcode' id = 'fromcode'>
          <input type='hidden' name = 'tocode' id = 'tocode'>
        </form>

  <a href="#" id="receivingUPC" class="list-group-item">UPC Receiving history</a>
    <form method = 'POST' action = '/csm/public/home/UPCReceivingHistory' name='upcReceivingform' id = 'upcReceivingform'>
      <input type='hidden' name = 'upcReceivingNumber' id = 'upcReceivingNumber'>
      <input type='hidden' name = 'fromReceivingupc' id = 'fromReceivingupc'>
      <input type='hidden' name = 'toReceivingupc' id = 'toReceivingupc'>
    </form>

    <a href="#" id="description" class="list-group-item">Item description</a>
      <form method = 'POST' action = '/csm/public/home/itemDescription' name='descriptionform' id = 'descriptionform'>
          <input type = 'hidden' name = 'itemDescription' id='itemDescription'>
          <input type='hidden' name = 'descriptionfrom' id = 'fromdescription'>
          <input type='hidden' name = 'descriptionto' id = 'todescription'>
      </form>

  <a href="#" id="vendorPrice" class="list-group-item">Vendor Price Compare</a>
    <form method = 'POST' action = '/csm/public/home/vendorPriceCompare' name='priceCompareForm' id = 'priceCompareForm'>
        <input type='hidden' name = 'vendor1' id = 'vendor1'>
        <input type='hidden' name = 'vendor2' id = 'vendor2'>
        <input type='hidden' name = 'fromPriceCompare' id = 'fromPriceCompare'>
        <input type='hidden' name = 'toPriceCompare' id = 'toPriceCompare'>
    </form>

  <a href="#" id="vendor" class="list-group-item">Vendor Section Final - Normal</a>
      <form method = 'POST' action = '/csm/public/home/vendor' name='vendorform' id = 'vendorform'>
          <input type='hidden' name = 'vendorNumber' id = 'vendorNumber'>
          <input type='hidden' name = 'fromvendor' id = 'fromvendor'>
          <input type='hidden' name = 'tovendor' id = 'tovendor'>
        </form>

    <a href="#" id="vendorNegative" class="list-group-item">Vendor Section Final - Negative</a>
      <form method = 'POST' action = '/csm/public/home/vendorNegative' name='vendorNegativeform' id = 'vendorNegativeform'>
          <input type='hidden' name = 'vendorNegNumber' id = 'vendorNegNumber'>
          <input type='hidden' name = 'fromNegvendor' id = 'fromNegvendor'>
          <input type='hidden' name = 'toNegvendor' id = 'toNegvendor'>
        </form>

    <a href="#" id="vendorSection" class="list-group-item">Vendor section</a>
    <form method = 'POST' action = '/csm/public/home/vendorSection' name='vendorSectionform' id = 'vendorSectionform'>
        <input type='hidden' name = 'svendorNumber' id = 'svendorNumber'>
        <input type='hidden' name = 'sctvendorNumber' id = 'sctvendorNumber'>
        <input type='hidden' name = 'fromvendorSection' id = 'fromvendorSection'>
        <input type='hidden' name = 'tovendorSection' id = 'tovendorSection'>
    </form>
    </div>