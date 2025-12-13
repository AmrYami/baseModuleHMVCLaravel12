var initDataTable = function(
    tableId,
    documentTitle = '',
    search = true,
    exportButtons = true
)
{
    var selector = "#" + tableId + "-table";
    var $tableEl = $(selector);
    if ($tableEl.length === 0) {
        return;
    }

    var table = $tableEl.DataTable({
        "info": false,
        'order': [],
        'pageLength': 10,
    });

    if(search){
        $("#"+tableId+"-field-search").on('keyup', function () {
            table.search(this.value).draw();
        });
    }

    if(exportButtons && $.fn.dataTable && $.fn.dataTable.Buttons){
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'copyHtml5',
                    title: documentTitle
                },
                {
                    extend: 'excelHtml5',
                    title: documentTitle
                },
                {
                    extend: 'csvHtml5',
                    title: documentTitle
                },
                {
                    extend: 'pdfHtml5',
                    title: documentTitle
                },
                {
                    extend: 'print',
                    title: documentTitle
                }
            ]
        }).container().appendTo($('#'+tableId+"-dt-buttons"));
        exportButtons = document.querySelectorAll('#'+tableId+"-export-menu [data-kt-export]");
            exportButtons.forEach(exportButton => {
                exportButton.addEventListener('click', e => {
                    e.preventDefault();

                    // Get clicked export value
                const exportValue = e.target.getAttribute('data-kt-export');
                const target = document.querySelector('.dt-buttons .buttons-' + exportValue);

                // Trigger click event on hidden datatable export buttons
                target.click();
            });
        });
    }
}

// Toggle email fields for admin actions
document.addEventListener('change', function (e) {
    var toggleTarget = e.target.getAttribute('data-toggle-email');
    if (!toggleTarget) return;
    var target = document.querySelector(toggleTarget);
    if (!target) return;
    if (e.target.checked) {
        target.classList.remove('d-none');
    } else {
        target.classList.add('d-none');
    }
});

// Toggle manual vs hospital email select
document.addEventListener('change', function (e) {
    var selectTarget = e.target.getAttribute('data-toggle-target');
    var hideTarget = e.target.getAttribute('data-toggle-hide');
    if (!selectTarget && !hideTarget) return;

    if (selectTarget) {
        var sel = document.querySelector(selectTarget);
        if (sel) {
            sel.classList.toggle('d-none', !e.target.checked);
            sel.querySelectorAll('input,select,textarea').forEach(function (el) {
                el.disabled = !e.target.checked;
            });
        }
    }
    if (hideTarget) {
        var hid = document.querySelector(hideTarget);
        if (hid) {
            hid.classList.toggle('d-none', e.target.checked);
            hid.querySelectorAll('input,select,textarea').forEach(function (el) {
                el.disabled = e.target.checked;
            });
        }
    }
});


// Stepper element (guard when component not on page or KTStepper not loaded)
var element = document.querySelector("#kt_stepper_example_vertical");
if (element && typeof KTStepper !== 'undefined') {
    // Initialize Stepper
    var stepper = new KTStepper(element);

    // Handle navigation click
    if (typeof stepper.on === 'function') {
        stepper.on("kt.stepper.click", function (stepper) {
            stepper.goTo(stepper.getClickedStepIndex()); // go to clicked step
        });

        // Handle next step
        stepper.on("kt.stepper.next", function (stepper) {
            stepper.goNext(); // go next step
        });

        // Handle previous step
        stepper.on("kt.stepper.previous", function (stepper) {
            stepper.goPrevious(); // go previous step
        });
    }
}
