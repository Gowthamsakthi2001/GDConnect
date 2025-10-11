$(function () {
    // your code here
    $details = JSON.parse($("#purchase-table tbody").attr("data-details"));
    if ($details.length > 0) {
        $details.forEach((item, i) => {
            console.log(item);
            let row = addNewRow("#purchase-table", item);
        });
    } else {
        addNewRow("#purchase-table");
    }
    calculateTotal();
    select2AjaxInit("#employee_id");
    select2AjaxInit("#vendor_id");
    select2AjaxInit("#vehicle_id");
    select2AjaxInit("#trip_type_id");
    select2AjaxInit("#maintenance_type_id");
});

function addNewRow(element, item = null) {
    let el = $(element);
    let expenseTypeUrl = el.data("type-url");
    let tbody = el.find("tbody");
    // count tr
    let i = tbody.find("tr").length;
    let row = `<tr id="row-${i}">`;
    row += `<td>
                <select name="collection[${i}][type_id]" id="type-id-${i}" class="form-control" data-ajax-url="${expenseTypeUrl}" data-placeholder="${localize(
        "Choose Expense Type"
    )}" required >`;

    if (item && item.type) {
        row += `<option value="${item.type.id}" selected >${item.type.name}</option>`;
    }
    row += `</select><label class="error" for="type-id-${i}"></label></td>`;
    row += `<td>
                <input type="number" class="form-control arrow-hidden" onclick="selectAll(this)" onchange="calculateTotal()" oninput="calculateTotal()"  id="quantity-${i}"
                name="collection[${i}][qty]"
                value="${item ? item.qty : "0"}" min="0"
                required>
            </td>`;
    row += `<td>
                <input type="number" class="form-control arrow-hidden" onclick="selectAll(this)" onchange="calculateTotal()" oninput="calculateTotal()"  id="unit_price-${i}"
                name="collection[${i}][price]"
                value="${item ? item.price : "0.00"}" step=".01" min="0.01"
                required>
            </td>`;
    row += `<td class="">
                    <input type="number" class="form-control text-end" id="sub-total-${i}" value="0.00" step=".01" disabled>
                </td>`;
    row += `<td>`;
    if (i == 0) {
        row += `<button type="button" class="btn btn-success my-1" onclick="addNewRow('${element}')">
                <i class="fa fa-plus-circle"></i>
            </button>`;
    } else {
        row += ` <button type="button" class="btn btn-danger my-1" onclick="deleteRow(${i})">
                <i class="fa fa-trash"></i>
            </button>`;
    }

    row += `</td>`;
    row += `</tr>`;

    tbody.append(row);
    expenseTypeSelect2AjaxInit(i);
    return i;
}

function deleteRow(i) {
    let row = $(`#row-${i}`);
    // check body length
    let body = row.find("tbody");
    if (body.find("tr").length < 1) {
        row.remove();
        calculateTotal();
    } else {
        alert("You can't delete the last row");
    }
}
function selectAll(el) {
    el.select();
}

function calculateTotal(element = "#purchase-table") {
    let table = $(element);
    let rows = table.find("tbody tr");
    let gt = 0;
    rows.each(function (i, row) {
        let q = 0,
            u = 0,
            st = 0;
        row = $(row);
        q = row.find(`#quantity-${i}`).val() ?? 0;
        u = row.find(`#unit_price-${i}`).val() ?? 0;
        st = parseFloat(q) * parseFloat(u);
        gt += st;
        row.find(`#sub-total-${i}`).val(st.toFixed(2));
    });
    $(".gross_total").val(gt.toFixed(2));
}

function expenseTypeSelect2AjaxInit(i) {
    element = `#type-id-${i}`;
    // Get all options value and text from the original select element
    let options = $(element)
        .find("option")
        .map(function () {
            return {
                id: $(this).val(),
                text: $(this).text(),
            };
        })
        .get(); // Convert jQuery map result to a plain array
    // Initialize select2 and fetch data using AJAX call
    $(element).select2({
        width: "100%",
        ajax: {
            placeholder: $(element).data("placeholder") ?? "",
            url: $(element).data("ajax-url") ?? "",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                results = data.data;
                // filter options using search term
                if (params.term) {
                    filterOptions = options.filter(function (item) {
                        return item.text
                            .toLowerCase()
                            .includes(params.term.toLowerCase());
                    });
                    results = filterOptions.concat(results);
                } else {
                    results = options.concat(results);
                }
                return {
                    results: results,
                    pagination: {
                        more: data.current_page < data.last_page,
                    },
                };
            },
            cache: true,
        },
        placeholder: localize("Search...") ?? "Search...",
        language: {
            searching: function () {
                return localize("Loading...") ?? "Loading...";
            },
        },
    });
}
