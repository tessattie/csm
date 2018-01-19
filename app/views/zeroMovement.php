<li class="dropdown">
          <a href="#" class="dropdown-toggle navrightmenu menuitems" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Zero Movement <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#" id="sectionMvt">Section</a>
            <form method = 'POST' action = '/csm/public/home/sectionMovement' name='sectionMvtform' id = 'sectionMvtform'>
              <input type='hidden' name = 'sectionMvtNumber' id = 'sectionMvtNumber'>
              <input type='hidden' name = 'fromMvtsection' id = 'fromMvtsection'>
              <input type='hidden' name = 'toMvtsection' id = 'toMvtsection'>
            </form></li>
            <li><a href="#" id="vendorSectionMvt">Vendor Section</a>
            <form method = 'POST' action = '/csm/public/home/vendorSectionMovement' name='vendorSectionMvtform' id = 'vendorSectionMvtform'>
              <input type='hidden' name = 'svendorMvtNumber' id = 'svendorMvtNumber'>
              <input type='hidden' name = 'sctvendorMvtNumber' id = 'sctvendorMvtNumber'>
              <input type='hidden' name = 'fromvendorMvtSection' id = 'fromvendorMvtSection'>
              <input type='hidden' name = 'tovendorMvtSection' id = 'tovendorMvtSection'>
            </form></li>

            <li><a href="#" id="dptMvt">Department - No receiving</a>
            <form method = 'POST' action = '/csm/public/home/departmentMovement' name='dptMvtform' id = 'dptMvtform'>
              <input type='hidden' name = 'dptMvtNumber' id = 'dptMvtNumber'>
              <input type='hidden' name = 'fromDptvendor' id = 'fromDptvendor'>
              <input type='hidden' name = 'toDptvendor' id = 'toDptvendor'>
            </form></li>
          </ul>
        </li>