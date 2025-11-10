$(function () {
    // your code here
    $details = JSON.parse($("#purchase-table tbody").attr("data-details"));
    if ($details.length > 0) {
        $details.forEach((item, i) => {
            let row = addNewRow("#purchase-table", item);
        });
    } else {
        addNewRow("#purchase-table");
    }
    calculateTotal();
    select2AjaxInit("#vehicle_id");
});

function addNewRow(element, item = null) {
    let el = $(element);
    let inventoryUrl = el.data("inventory-category-url");
    let partsUrl = el.data("parts-url");
    let tbody = el.find("tbody");
    // count tr
    let i = tbody.find("tr").length;
    let row = `<tr id="row-${i}">`;
    row += `<td>
                <select name="collection[${i}][category_id]" id="category-id-${i}" class="form-control" data-ajax-url="${inventoryUrl}" onchange="changeCategory(${i})" data-placeholder="${localize(
        "Choose Inventory Category"
    )}" required >`;

    if (item && item.category) {
        row += `<option value="${item.category.id}" selected >${item.category.name}</option>`;
    }
    row += `</select><label class="error" for="category-id-${i}"></label></td>`;
    row += `<td>
                <select name="collection[${i}][parts_id]" id="parts-id-${i}" class="form-control" data-ajax-url="${partsUrl}" data-placeholder="${localize(
        "Choose Parts"
    )}"  required>`;

    if (item && item.parts) {
        row += `<option value="${item.parts.id}" selected >${item.parts.name}</option>`;
    }
    row += `</select><label class="error" for="parts-id-${i}"></label></td>`;
    row += `<td>
                <input type="number" class="form-control arrow-hidden" onclick="selectAll(this)" onchange="calculateTotal()" oninput="calculateTotal()"  id="quantity-${i}"
                name="collection[${i}][qty]"
                value="${item ? item.qty : "00"}"   min="0"
                required>
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
    categorySelect2AjaxInit(i);
    partsSelect2AjaxInit(i);
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
        let q = 0;
        row = $(row);
        q = row.find(`#quantity-${i}`).val() ?? 0;
        gt += parseFloat(q);
    });
    $(".gross_total").val(gt.toFixed(2));
}

function categorySelect2AjaxInit(i) {
    element = `#category-id-${i}`;
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

function partsSelect2AjaxInit(i) {
    element = `#parts-id-${i}`;

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
                    category_id: $(`#category-id-${i}`).val(),
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

function changeCategory(i) {
    // parts select value clear
    $(`#parts-id-${i}`).val(null).trigger("change");
}
