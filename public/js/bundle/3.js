(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[3],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/src/views/app/pages/quotations/index_quotation.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/src/views/app/pages/quotations/index_quotation.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vuex__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vuex */ "./node_modules/vuex/dist/vuex.esm.js");
/* harmony import */ var nprogress__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! nprogress */ "./node_modules/nprogress/nprogress.js");
/* harmony import */ var nprogress__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(nprogress__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var jspdf__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! jspdf */ "./node_modules/jspdf/dist/jspdf.es.min.js");
/* harmony import */ var jspdf_autotable__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! jspdf-autotable */ "./node_modules/jspdf-autotable/dist/jspdf.plugin.autotable.js");
/* harmony import */ var jspdf_autotable__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(jspdf_autotable__WEBPACK_IMPORTED_MODULE_3__);
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) { symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); } keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//




/* harmony default export */ __webpack_exports__["default"] = ({
  metaInfo: {
    title: "Quotation"
  },
  data: function data() {
    return {
      isLoading: true,
      serverParams: {
        sort: {
          field: "id",
          type: "desc"
        },
        page: 1,
        perPage: 10
      },
      selectedIds: [],
      totalRows: "",
      search: "",
      showDropdown: false,
      Filter_date: "",
      Filter_client: "",
      Filter_status: "",
      Filter_Ref: "",
      Filter_warehouse: "",
      customers: [],
      warehouses: [],
      details: [],
      quotations: [],
      quote: {},
      limit: "10",
      email: {
        to: "",
        subject: "",
        message: "",
        client_name: "",
        quote_Ref: ""
      }
    };
  },
  mounted: function mounted() {
    var _this = this;

    this.$root.$on("bv::dropdown::show", function (bvEvent) {
      _this.showDropdown = true;
    });
    this.$root.$on("bv::dropdown::hide", function (bvEvent) {
      _this.showDropdown = false;
    });
  },
  computed: _objectSpread(_objectSpread({}, Object(vuex__WEBPACK_IMPORTED_MODULE_0__["mapGetters"])(["currentUserPermissions", "currentUser"])), {}, {
    columns: function columns() {
      return [{
        label: this.$t("date"),
        field: "date",
        tdClass: "text-left",
        thClass: "text-left"
      }, {
        label: this.$t("Reference"),
        field: "Ref",
        tdClass: "text-left",
        thClass: "text-left"
      }, {
        label: this.$t("Customer"),
        field: "client_name",
        tdClass: "text-left",
        thClass: "text-left"
      }, {
        label: this.$t("warehouse"),
        field: "warehouse_name",
        tdClass: "text-left",
        thClass: "text-left"
      }, {
        label: this.$t("Status"),
        field: "statut",
        html: true,
        tdClass: "text-left",
        thClass: "text-left"
      }, {
        label: this.$t("Total"),
        field: "GrandTotal",
        type: "decimal",
        tdClass: "text-left",
        thClass: "text-left"
      }, {
        label: this.$t("Action"),
        field: "actions",
        html: true,
        tdClass: "text-right",
        thClass: "text-right",
        sortable: false
      }];
    }
  }),
  methods: {
    updateParams: function updateParams(newProps) {
      this.serverParams = Object.assign({}, this.serverParams, newProps);
    },
    //---- Event Page Change
    onPageChange: function onPageChange(_ref) {
      var currentPage = _ref.currentPage;

      if (this.serverParams.page !== currentPage) {
        this.updateParams({
          page: currentPage
        });
        this.Get_Quotations(currentPage);
      }
    },
    //---- Event Per Page Change
    onPerPageChange: function onPerPageChange(_ref2) {
      var currentPerPage = _ref2.currentPerPage;

      if (this.limit !== currentPerPage) {
        this.limit = currentPerPage;
        this.updateParams({
          page: 1,
          perPage: currentPerPage
        });
        this.Get_Quotations(1);
      }
    },
    //---- Event Select Rows
    selectionChanged: function selectionChanged(_ref3) {
      var _this2 = this;

      var selectedRows = _ref3.selectedRows;
      this.selectedIds = [];
      selectedRows.forEach(function (row, index) {
        _this2.selectedIds.push(row.id);
      });
    },
    //---- Event Sort
    onSortChange: function onSortChange(params) {
      var field = "";

      if (params[0].field == "client_name") {
        field = "client_id";
      } else if (params[0].field == "warehouse_name") {
        field = "warehouse_id";
      } else {
        field = params[0].field;
      }

      this.updateParams({
        sort: {
          type: params[0].type,
          field: field
        }
      });
      this.Get_Quotations(this.serverParams.page);
    },
    //---- Event Search
    onSearch: function onSearch(value) {
      this.search = value.searchTerm;
      this.Get_Quotations(this.serverParams.page);
    },
    //------ Toast
    makeToast: function makeToast(variant, msg, title) {
      this.$root.$bvToast.toast(msg, {
        title: title,
        variant: variant,
        solid: true
      });
    },
    //------ Reset Filter
    Reset_Filter: function Reset_Filter() {
      this.search = "";
      this.Filter_date = "";
      this.Filter_client = "";
      this.Filter_status = "";
      this.Filter_Ref = "";
      this.Filter_warehouse = "", this.Get_Quotations(this.serverParams.page);
    },
    //------------------------------------- Quotations PDF -------------------------\\
    Quotation_PDF: function Quotation_PDF() {
      var self = this;
      var pdf = new jspdf__WEBPACK_IMPORTED_MODULE_2__["default"]("p", "pt");
      var columns = [{
        title: "Date",
        dataKey: "date"
      }, {
        title: "Ref",
        dataKey: "Ref"
      }, {
        title: "Client",
        dataKey: "client_name"
      }, {
        title: "Status",
        dataKey: "statut"
      }, {
        title: "Total",
        dataKey: "GrandTotal"
      }];
      pdf.autoTable(columns, self.quotations);
      pdf.text("Quotation List", 40, 25);
      pdf.save("Quotation_List.pdf");
    },
    //----------------------------------- print Quotations Excel -------------------------\\
    Quotation_Excel: function Quotation_Excel() {
      // Start the progress bar.
      nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.start();
      nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.set(0.1);
      axios.get("quotations/export/Excel", {
        responseType: "blob",
        // important
        headers: {
          "Content-Type": "application/json"
        }
      }).then(function (response) {
        var url = window.URL.createObjectURL(new Blob([response.data]));
        var link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "List_Quotations.xlsx");
        document.body.appendChild(link);
        link.click(); // Complete the animation of the  progress bar.

        setTimeout(function () {
          return nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.done();
        }, 500);
      })["catch"](function () {
        // Complete the animation of the  progress bar.
        setTimeout(function () {
          return nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.done();
        }, 500);
      });
    },
    //----------------------------------- Quotation PDF by id -------------------------\\
    Quote_pdf: function Quote_pdf(quote, id) {
      // Start the progress bar.
      nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.start();
      nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.set(0.1);
      axios.get("Quote_PDF/" + id, {
        responseType: "blob",
        // important
        headers: {
          "Content-Type": "application/json"
        }
      }).then(function (response) {
        var url = window.URL.createObjectURL(new Blob([response.data]));
        var link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "Quotation_" + quote.Ref + ".pdf");
        document.body.appendChild(link);
        link.click(); // Complete the animation of the  progress bar.

        setTimeout(function () {
          return nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.done();
        }, 500);
      })["catch"](function () {
        // Complete the animation of the  progress bar.
        setTimeout(function () {
          return nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.done();
        }, 500);
      });
    },
    //------------------------------------ Form Send Quotation in  Email -------------------------\\
    QuoteEmail: function QuoteEmail(quote) {
      this.email.to = quote.client_email;
      this.email.quote_Ref = quote.Ref;
      this.email.client_name = quote.client_name;
      this.SendEmail(quote.id);
    },
    SendEmail: function SendEmail(id) {
      var _this3 = this;

      // Start the progress bar.
      nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.start();
      nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.set(0.1);
      axios.post("quotations/sendQuote/email", {
        id: id,
        to: this.email.to,
        client_name: this.email.client_name,
        Ref: this.email.quote_Ref
      }).then(function (response) {
        // Complete the animation of the  progress bar.
        setTimeout(function () {
          return nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.done();
        }, 500);

        _this3.makeToast("success", _this3.$t("Send.TitleEmail"), _this3.$t("Success"));
      })["catch"](function (error) {
        // Complete the animation of the  progress bar.
        setTimeout(function () {
          return nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.done();
        }, 500);

        _this3.makeToast("danger", _this3.$t("SMTPIncorrect"), _this3.$t("Failed"));
      });
    },
    //---------------------------------------- Set To Strings-------------------------\\
    setToStrings: function setToStrings() {
      // Simply replaces null values with strings=''s
      if (this.Filter_client === null) {
        this.Filter_client = "";
      } else if (this.Filter_warehouse === null) {
        this.Filter_warehouse = "";
      } else if (this.Filter_status === null) {
        this.Filter_status = "";
      }
    },
    //---------------------------------------- Get All Quotations -------------------------\\
    Get_Quotations: function Get_Quotations(page) {
      var _this4 = this;

      // Start the progress bar.
      nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.start();
      nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.set(0.1);
      this.setToStrings();
      axios.get("quotations?page=" + this.serverParams.page + "&Ref=" + this.Filter_Ref + "&client_id=" + this.Filter_client + "&statut=" + this.Filter_status + "&warehouse_id=" + this.Filter_warehouse + "&date=" + this.Filter_date + "&SortField=" + this.serverParams.sort.field + "&SortType=" + this.serverParams.sort.type + "&search=" + this.search + "&limit=" + this.limit).then(function (response) {
        _this4.quotations = response.data.quotations;
        _this4.customers = response.data.customers;
        _this4.warehouses = response.data.warehouses;
        _this4.totalRows = response.data.totalRows; // Complete the animation of theprogress bar.

        nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.done();
        _this4.isLoading = false;
      })["catch"](function (response) {
        // Complete the animation of theprogress bar.
        nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.done();
        setTimeout(function () {
          _this4.isLoading = false;
        }, 500);
      });
    },
    //-------------------------------------------- Delete Quotation -------------------------\\
    Remove_Quotation: function Remove_Quotation(id) {
      var _this5 = this;

      this.$swal({
        title: this.$t("Delete.Title"),
        text: this.$t("Delete.Text"),
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: this.$t("Delete.cancelButtonText"),
        confirmButtonText: this.$t("Delete.confirmButtonText")
      }).then(function (result) {
        if (result.value) {
          // Start the progress bar.
          nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.start();
          nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.set(0.1);
          axios["delete"]("quotations/" + id).then(function () {
            _this5.$swal(_this5.$t("Delete.Deleted"), _this5.$t("Delete.QuoteDeleted"), "success");

            Fire.$emit("Delete_Quote");
          })["catch"](function () {
            // Complete the animation of the  progress bar.
            setTimeout(function () {
              return nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.done();
            }, 500);

            _this5.$swal(_this5.$t("Delete.Failed"), _this5.$t("Delete.Therewassomethingwronge"), "warning");
          });
        }
      });
    },
    //---- Delete quotations by selection
    delete_by_selected: function delete_by_selected() {
      var _this6 = this;

      this.$swal({
        title: this.$t("Delete.Title"),
        text: this.$t("Delete.Text"),
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: this.$t("Delete.cancelButtonText"),
        confirmButtonText: this.$t("Delete.confirmButtonText")
      }).then(function (result) {
        if (result.value) {
          // Start the progress bar.
          nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.start();
          nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.set(0.1);
          axios.post("quotations/delete/by_selection", {
            selectedIds: _this6.selectedIds
          }).then(function () {
            _this6.$swal(_this6.$t("Delete.Deleted"), _this6.$t("Delete.QuoteDeleted"), "success");

            Fire.$emit("Delete_Quote");
          })["catch"](function () {
            // Complete the animation of theprogress bar.
            setTimeout(function () {
              return nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.done();
            }, 500);

            _this6.$swal(_this6.$t("Delete.Failed"), _this6.$t("Delete.Therewassomethingwronge"), "warning");
          });
        }
      });
    }
  },
  //-----------------------------Autoload function-------------------
  created: function created() {
    var _this7 = this;

    this.Get_Quotations(1);
    Fire.$on("Delete_Quote", function () {
      setTimeout(function () {
        _this7.Get_Quotations(_this7.serverParams.page); // Complete the animation of the  progress bar.


        setTimeout(function () {
          return nprogress__WEBPACK_IMPORTED_MODULE_1___default.a.done();
        }, 500);
      }, 500);
    });
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/src/views/app/pages/quotations/index_quotation.vue?vue&type=template&id=93ea2858&":
/*!***********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/src/views/app/pages/quotations/index_quotation.vue?vue&type=template&id=93ea2858& ***!
  \***********************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "main-content" },
    [
      _c("breadcumb", {
        attrs: { page: _vm.$t("ListQuotations"), folder: _vm.$t("Quotations") }
      }),
      _vm._v(" "),
      _vm.isLoading
        ? _c("div", {
            staticClass: "loading_page spinner spinner-primary mr-3"
          })
        : _c(
            "div",
            [
              _c(
                "vue-good-table",
                {
                  attrs: {
                    mode: "remote",
                    columns: _vm.columns,
                    totalRows: _vm.totalRows,
                    rows: _vm.quotations,
                    "search-options": {
                      enabled: true,
                      placeholder: _vm.$t("Search_this_table")
                    },
                    "select-options": {
                      enabled: true,
                      clearSelectionText: ""
                    },
                    "pagination-options": {
                      enabled: true,
                      mode: "records",
                      nextLabel: "next",
                      prevLabel: "prev"
                    },
                    styleClass: _vm.showDropdown
                      ? "tableOne table-hover vgt-table full-height"
                      : "tableOne table-hover vgt-table non-height"
                  },
                  on: {
                    "on-page-change": _vm.onPageChange,
                    "on-per-page-change": _vm.onPerPageChange,
                    "on-sort-change": _vm.onSortChange,
                    "on-search": _vm.onSearch,
                    "on-selected-rows-change": _vm.selectionChanged
                  },
                  scopedSlots: _vm._u([
                    {
                      key: "table-row",
                      fn: function(props) {
                        return [
                          props.column.field == "actions"
                            ? _c("span", [
                                _c(
                                  "div",
                                  [
                                    _c(
                                      "b-dropdown",
                                      {
                                        attrs: {
                                          id: "dropdown-left",
                                          variant: "link",
                                          text: "Left align",
                                          "toggle-class":
                                            "text-decoration-none",
                                          size: "lg",
                                          "no-caret": ""
                                        },
                                        scopedSlots: _vm._u(
                                          [
                                            {
                                              key: "button-content",
                                              fn: function() {
                                                return [
                                                  _c("span", {
                                                    staticClass:
                                                      "_dot _r_block-dot bg-dark"
                                                  }),
                                                  _vm._v(" "),
                                                  _c("span", {
                                                    staticClass:
                                                      "_dot _r_block-dot bg-dark"
                                                  }),
                                                  _vm._v(" "),
                                                  _c("span", {
                                                    staticClass:
                                                      "_dot _r_block-dot bg-dark"
                                                  })
                                                ]
                                              },
                                              proxy: true
                                            }
                                          ],
                                          null,
                                          true
                                        )
                                      },
                                      [
                                        _vm._v(" "),
                                        _c(
                                          "b-navbar-nav",
                                          [
                                            _c(
                                              "b-dropdown-item",
                                              {
                                                attrs: {
                                                  title: "Show",
                                                  to:
                                                    "/app/quotations/detail/" +
                                                    props.row.id
                                                }
                                              },
                                              [
                                                _c("i", {
                                                  staticClass:
                                                    "nav-icon i-Eye font-weight-bold mr-2"
                                                }),
                                                _vm._v(
                                                  "\n                  " +
                                                    _vm._s(
                                                      _vm.$t("DetailQuote")
                                                    ) +
                                                    "\n                "
                                                )
                                              ]
                                            )
                                          ],
                                          1
                                        ),
                                        _vm._v(" "),
                                        _vm.currentUserPermissions.includes(
                                          "Quotations_edit"
                                        )
                                          ? _c(
                                              "b-dropdown-item",
                                              {
                                                attrs: {
                                                  title: "Edit",
                                                  to:
                                                    "/app/quotations/edit/" +
                                                    props.row.id
                                                }
                                              },
                                              [
                                                _c("i", {
                                                  staticClass:
                                                    "nav-icon i-Pen-2 font-weight-bold mr-2"
                                                }),
                                                _vm._v(
                                                  "\n                " +
                                                    _vm._s(
                                                      _vm.$t("EditQuote")
                                                    ) +
                                                    "\n              "
                                                )
                                              ]
                                            )
                                          : _vm._e(),
                                        _vm._v(" "),
                                        _vm.currentUserPermissions.includes(
                                          "Quotations_edit"
                                        )
                                          ? _c(
                                              "b-dropdown-item",
                                              {
                                                attrs: {
                                                  title: "Create Sale",
                                                  to:
                                                    "/app/quotations/Create_sale/" +
                                                    props.row.id
                                                }
                                              },
                                              [
                                                _c("i", {
                                                  staticClass:
                                                    "nav-icon i-Add font-weight-bold mr-2"
                                                }),
                                                _vm._v(
                                                  "\n                " +
                                                    _vm._s(
                                                      _vm.$t("CreateSale")
                                                    ) +
                                                    "\n              "
                                                )
                                              ]
                                            )
                                          : _vm._e(),
                                        _vm._v(" "),
                                        _c(
                                          "b-dropdown-item",
                                          {
                                            attrs: { title: "PDF" },
                                            on: {
                                              click: function($event) {
                                                return _vm.Quote_pdf(
                                                  props.row,
                                                  props.row.id
                                                )
                                              }
                                            }
                                          },
                                          [
                                            _c("i", {
                                              staticClass:
                                                "nav-icon i-File-TXT font-weight-bold mr-2"
                                            }),
                                            _vm._v(
                                              "\n                " +
                                                _vm._s(_vm.$t("DownloadPdf")) +
                                                "\n              "
                                            )
                                          ]
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "b-dropdown-item",
                                          {
                                            attrs: { title: "Email" },
                                            on: {
                                              click: function($event) {
                                                return _vm.QuoteEmail(
                                                  props.row,
                                                  props.row.id
                                                )
                                              }
                                            }
                                          },
                                          [
                                            _c("i", {
                                              staticClass:
                                                "nav-icon i-Envelope-2 font-weight-bold mr-2"
                                            }),
                                            _vm._v(
                                              "\n                " +
                                                _vm._s(_vm.$t("QuoteEmail")) +
                                                "\n              "
                                            )
                                          ]
                                        ),
                                        _vm._v(" "),
                                        _vm.currentUserPermissions.includes(
                                          "Quotations_delete"
                                        )
                                          ? _c(
                                              "b-dropdown-item",
                                              {
                                                attrs: { title: "Delete" },
                                                on: {
                                                  click: function($event) {
                                                    return _vm.Remove_Quotation(
                                                      props.row.id
                                                    )
                                                  }
                                                }
                                              },
                                              [
                                                _c("i", {
                                                  staticClass:
                                                    "nav-icon i-Close-Window font-weight-bold mr-2"
                                                }),
                                                _vm._v(
                                                  "\n                " +
                                                    _vm._s(
                                                      _vm.$t("DeleteQuote")
                                                    ) +
                                                    "\n              "
                                                )
                                              ]
                                            )
                                          : _vm._e()
                                      ],
                                      1
                                    )
                                  ],
                                  1
                                )
                              ])
                            : props.column.field == "statut"
                            ? _c("div", [
                                props.row.statut == "sent"
                                  ? _c(
                                      "span",
                                      {
                                        staticClass:
                                          "badge badge-outline-success"
                                      },
                                      [_vm._v(_vm._s(_vm.$t("Sent")))]
                                    )
                                  : _c(
                                      "span",
                                      {
                                        staticClass: "badge badge-outline-info"
                                      },
                                      [_vm._v(_vm._s(_vm.$t("Pending")))]
                                    )
                              ])
                            : _vm._e()
                        ]
                      }
                    }
                  ])
                },
                [
                  _c(
                    "div",
                    {
                      attrs: { slot: "selected-row-actions" },
                      slot: "selected-row-actions"
                    },
                    [
                      _c(
                        "button",
                        {
                          staticClass: "btn btn-danger btn-sm",
                          on: {
                            click: function($event) {
                              return _vm.delete_by_selected()
                            }
                          }
                        },
                        [_vm._v(_vm._s(_vm.$t("Del")))]
                      )
                    ]
                  ),
                  _vm._v(" "),
                  _c(
                    "div",
                    {
                      staticClass: "mt-2 mb-3",
                      attrs: { slot: "table-actions" },
                      slot: "table-actions"
                    },
                    [
                      _c(
                        "b-button",
                        {
                          directives: [
                            {
                              name: "b-toggle",
                              rawName: "v-b-toggle.sidebar-right",
                              modifiers: { "sidebar-right": true }
                            }
                          ],
                          attrs: {
                            variant: "outline-info ripple m-1",
                            size: "sm"
                          }
                        },
                        [
                          _c("i", { staticClass: "i-Filter-2" }),
                          _vm._v(
                            "\n          " +
                              _vm._s(_vm.$t("Filter")) +
                              "\n        "
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "b-button",
                        {
                          attrs: {
                            size: "sm",
                            variant: "outline-success ripple m-1"
                          },
                          on: {
                            click: function($event) {
                              return _vm.Quotation_PDF()
                            }
                          }
                        },
                        [
                          _c("i", { staticClass: "i-File-Copy" }),
                          _vm._v(" PDF\n        ")
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "b-button",
                        {
                          attrs: {
                            size: "sm",
                            variant: "outline-danger ripple m-1"
                          },
                          on: {
                            click: function($event) {
                              return _vm.Quotation_Excel()
                            }
                          }
                        },
                        [
                          _c("i", { staticClass: "i-File-Excel" }),
                          _vm._v(" EXCEL\n        ")
                        ]
                      ),
                      _vm._v(" "),
                      _vm.currentUserPermissions &&
                      _vm.currentUserPermissions.includes("Quotations_add")
                        ? _c(
                            "router-link",
                            {
                              staticClass:
                                "btn-sm btn btn-primary ripple btn-icon m-1",
                              attrs: { to: "/app/quotations/store" }
                            },
                            [
                              _c("span", { staticClass: "ul-btn__icon" }, [
                                _c("i", { staticClass: "i-Add" })
                              ]),
                              _vm._v(" "),
                              _c("span", { staticClass: "ul-btn__text ml-1" }, [
                                _vm._v(_vm._s(_vm.$t("Add")))
                              ])
                            ]
                          )
                        : _vm._e()
                    ],
                    1
                  )
                ]
              )
            ],
            1
          ),
      _vm._v(" "),
      _c(
        "b-sidebar",
        {
          attrs: {
            id: "sidebar-right",
            title: _vm.$t("Filter"),
            "bg-variant": "white",
            right: "",
            shadow: ""
          }
        },
        [
          _c(
            "div",
            { staticClass: "px-3 py-2" },
            [
              _c(
                "b-row",
                [
                  _c(
                    "b-col",
                    { attrs: { md: "12" } },
                    [
                      _c(
                        "b-form-group",
                        { attrs: { label: _vm.$t("date") } },
                        [
                          _c("b-form-input", {
                            attrs: { type: "date" },
                            model: {
                              value: _vm.Filter_date,
                              callback: function($$v) {
                                _vm.Filter_date = $$v
                              },
                              expression: "Filter_date"
                            }
                          })
                        ],
                        1
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "b-col",
                    { attrs: { md: "12" } },
                    [
                      _c(
                        "b-form-group",
                        { attrs: { label: _vm.$t("Reference") } },
                        [
                          _c("b-form-input", {
                            attrs: {
                              label: "Reference",
                              placeholder: _vm.$t("Reference")
                            },
                            model: {
                              value: _vm.Filter_Ref,
                              callback: function($$v) {
                                _vm.Filter_Ref = $$v
                              },
                              expression: "Filter_Ref"
                            }
                          })
                        ],
                        1
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "b-col",
                    { attrs: { md: "12" } },
                    [
                      _c(
                        "b-form-group",
                        { attrs: { label: _vm.$t("Customer") } },
                        [
                          _c("v-select", {
                            attrs: {
                              reduce: function(label) {
                                return label.value
                              },
                              placeholder: _vm.$t("Choose_Customer"),
                              options: _vm.customers.map(function(customers) {
                                return {
                                  label: customers.name,
                                  value: customers.id
                                }
                              })
                            },
                            model: {
                              value: _vm.Filter_client,
                              callback: function($$v) {
                                _vm.Filter_client = $$v
                              },
                              expression: "Filter_client"
                            }
                          })
                        ],
                        1
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "b-col",
                    { attrs: { md: "12" } },
                    [
                      _c(
                        "b-form-group",
                        { attrs: { label: _vm.$t("warehouse") } },
                        [
                          _c("v-select", {
                            attrs: {
                              reduce: function(label) {
                                return label.value
                              },
                              placeholder: _vm.$t("Choose_Warehouse"),
                              options: _vm.warehouses.map(function(warehouses) {
                                return {
                                  label: warehouses.name,
                                  value: warehouses.id
                                }
                              })
                            },
                            model: {
                              value: _vm.Filter_warehouse,
                              callback: function($$v) {
                                _vm.Filter_warehouse = $$v
                              },
                              expression: "Filter_warehouse"
                            }
                          })
                        ],
                        1
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "b-col",
                    { attrs: { md: "12" } },
                    [
                      _c(
                        "b-form-group",
                        { attrs: { label: _vm.$t("Status") } },
                        [
                          _c("v-select", {
                            attrs: {
                              reduce: function(label) {
                                return label.value
                              },
                              placeholder: _vm.$t("Choose_Status"),
                              options: [
                                { label: "Sent", value: "sent" },
                                { label: "Pending", value: "pending" }
                              ]
                            },
                            model: {
                              value: _vm.Filter_status,
                              callback: function($$v) {
                                _vm.Filter_status = $$v
                              },
                              expression: "Filter_status"
                            }
                          })
                        ],
                        1
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "b-col",
                    { attrs: { md: "6", sm: "12" } },
                    [
                      _c(
                        "b-button",
                        {
                          attrs: {
                            variant: "primary ripple m-1",
                            size: "sm",
                            block: ""
                          },
                          on: {
                            click: function($event) {
                              return _vm.Get_Quotations(_vm.serverParams.page)
                            }
                          }
                        },
                        [
                          _c("i", { staticClass: "i-Filter-2" }),
                          _vm._v(
                            "\n            " +
                              _vm._s(_vm.$t("Filter")) +
                              "\n          "
                          )
                        ]
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "b-col",
                    { attrs: { md: "6", sm: "12" } },
                    [
                      _c(
                        "b-button",
                        {
                          attrs: {
                            variant: "danger ripple m-1",
                            size: "sm",
                            block: ""
                          },
                          on: {
                            click: function($event) {
                              return _vm.Reset_Filter()
                            }
                          }
                        },
                        [
                          _c("i", { staticClass: "i-Power-2" }),
                          _vm._v(
                            "\n            " +
                              _vm._s(_vm.$t("Reset")) +
                              "\n          "
                          )
                        ]
                      )
                    ],
                    1
                  )
                ],
                1
              )
            ],
            1
          )
        ]
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/src/views/app/pages/quotations/index_quotation.vue":
/*!**********************************************************************!*\
  !*** ./resources/src/views/app/pages/quotations/index_quotation.vue ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _index_quotation_vue_vue_type_template_id_93ea2858___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./index_quotation.vue?vue&type=template&id=93ea2858& */ "./resources/src/views/app/pages/quotations/index_quotation.vue?vue&type=template&id=93ea2858&");
/* harmony import */ var _index_quotation_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./index_quotation.vue?vue&type=script&lang=js& */ "./resources/src/views/app/pages/quotations/index_quotation.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _index_quotation_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _index_quotation_vue_vue_type_template_id_93ea2858___WEBPACK_IMPORTED_MODULE_0__["render"],
  _index_quotation_vue_vue_type_template_id_93ea2858___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/src/views/app/pages/quotations/index_quotation.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/src/views/app/pages/quotations/index_quotation.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************!*\
  !*** ./resources/src/views/app/pages/quotations/index_quotation.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_index_quotation_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./index_quotation.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/src/views/app/pages/quotations/index_quotation.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_index_quotation_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/src/views/app/pages/quotations/index_quotation.vue?vue&type=template&id=93ea2858&":
/*!*****************************************************************************************************!*\
  !*** ./resources/src/views/app/pages/quotations/index_quotation.vue?vue&type=template&id=93ea2858& ***!
  \*****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_index_quotation_vue_vue_type_template_id_93ea2858___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./index_quotation.vue?vue&type=template&id=93ea2858& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/src/views/app/pages/quotations/index_quotation.vue?vue&type=template&id=93ea2858&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_index_quotation_vue_vue_type_template_id_93ea2858___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_index_quotation_vue_vue_type_template_id_93ea2858___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);