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

  <a href="#" id="sectionPrice" class="list-group-item">Section Price Compare</a>
    <form method = 'POST' action = '/csm/public/home/sectionPriceCompare' name='SectionPriceCompareForm' id = 'SectionPriceCompareForm'>
        <input type='hidden' name = 'vendor1Section' id = 'vendor1Section'>
        <input type='hidden' name = 'vendor2Section' id = 'vendor2Section'>
        <input type='hidden' name = 'sectionCompare' id = 'sectionCompare'>
        <input type='hidden' name = 'fromSectionCompare' id = 'fromSectionCompare'>
        <input type='hidden' name = 'toSectionCompare' id = 'toSectionCompare'>
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

    <a href="#" id="vendorSection" class="list-group-item">Vendor Section - Normal</a>
    <form method = 'POST' action = '/csm/public/home/vendorSection' name='vendorSectionform' id = 'vendorSectionform'>
        <input type='hidden' name = 'svendorNumber' id = 'svendorNumber'>
        <input type='hidden' name = 'sctvendorNumber' id = 'sctvendorNumber'>
        <input type='hidden' name = 'fromvendorSection' id = 'fromvendorSection'>
        <input type='hidden' name = 'tovendorSection' id = 'tovendorSection'>
    </form>

    <a href="#" id="vendorSectionNegative" class="list-group-item">Vendor Section - Negative</a>
    <form method = 'POST' action = '/csm/public/home/vendorSectionNegative' name='vendorSectionNegativeform' id = 'vendorSectionNegativeform'>
        <input type='hidden' name = 'svendorNegNumber' id = 'svendorNegNumber'>
        <input type='hidden' name = 'sctvendorNegNumber' id = 'sctvendorNegNumber'>
        <input type='hidden' name = 'fromvendorNegSection' id = 'fromvendorNegSection'>
        <input type='hidden' name = 'tovendorNegSection' id = 'tovendorNegSection'>
    </form>

  <a href="#" id="section" class="list-group-item">Section - Normal</a>
    <form method = 'POST' action = '/csm/public/home/section' name='sectionform' id = 'sectionform'>
        <input type='hidden' name = 'sectionNumber' id = 'sectionNumber'>
        <input type='hidden' name = 'fromsection' id = 'fromsection'>
        <input type='hidden' name = 'tosection' id = 'tosection'>
    </form>

  <a href="#" id="sectionNegative" class="list-group-item">Section Negative for Inventory</a>
    <form method = 'POST' action = '/csm/public/home/sectionNegative' name='sectionNegform' id = 'sectionNegform'>
        <input type='hidden' name = 'sectionNegNumber' id = 'sectionNegNumber'>
        <input type='hidden' name = 'fromNegsection' id = 'fromNegsection'>
        <input type='hidden' name = 'toNegsection' id = 'toNegsection'>
    </form>

  <a href="#" id="vendorDepartment" class="list-group-item">Vendor Department - Normal</a>
    <form method = 'POST' action = '/csm/public/home/vendorDepartment' name='vendorDepartmentform' id = 'vendorDepartmentform'>
        <input type='hidden' name = 'dvendorNumber' id = 'dvendorNumber'>
        <input type='hidden' name = 'dptvendorNumber' id = 'dptvendorNumber'>
        <input type='hidden' name = 'fromvendorDpt' id = 'fromvendorDpt'>
        <input type='hidden' name = 'tovendorDpt' id = 'tovendorDpt'>
    </form>

  <a href="#" id="vendorDepartmentNeg" class="list-group-item">Vendor Department - Negative</a>
    <form method = 'POST' action = '/csm/public/home/vendorDepartmentNegative' name='vendorDepartmentformNeg' id = 'vendorDepartmentformNeg'>
        <input type='hidden' name = 'dvendorNumberNeg' id = 'dvendorNumberNeg'>
        <input type='hidden' name = 'dptvendorNumberNeg' id = 'dptvendorNumberNeg'>
        <input type='hidden' name = 'fromvendorDptNeg' id = 'fromvendorDptNeg'>
        <input type='hidden' name = 'tovendorDptNeg' id = 'tovendorDptNeg'>
    </form>

  <a href="#" id="department" class="list-group-item">Department</a>
    <form method = 'POST' action = '/csm/public/home/department' name='departmentform' id = 'departmentform'>
        <input type='hidden' name = 'departmentNumber' id = 'departmentNumber'>
        <input type='hidden' name = 'fromdepartment' id = 'fromdepartment'>
        <input type='hidden' name = 'todepartment' id = 'todepartment'>
    </form>

  <a href="#" id="upcRange" class="list-group-item">UPC Range</a>
    <form method = 'POST' action = '/csm/public/home/UPCRange' name='upcRangeform' id = 'upcRangeform'>
        <input type='hidden' name = 'upcRangeNo1' id = 'upcRangeNo1'>
        <input type='hidden' name = 'upcRangeNo2' id = 'upcRangeNo2'>
        <input type='hidden' name = 'fromupcRange' id = 'fromupcRange'>
        <input type='hidden' name = 'toupcRange' id = 'toupcRange'>
    </form>
    </div>