<template>
  <div class="projects">
    <div class="hide-cm">
      <span>Hide Column: </span>
      <span v-for="column in columns" :key="column.name">
        <input
          type="checkbox"
          :disabled="visibleFields.length == 1 && column.visible"
          :id="column.label"
          :value="column.name"
          v-model="column.visible"
        />
        <label class="lb" :for="column.label">{{ column.label }}</label>
      </span>
      <a class="download-pg2 is-small pagination-previous" @click="generateCsv">
        Download Csv
      </a>
      <a
        class="download-pg2 is-small pagination-previous"
        @click="generateExcel"
      >
        Download Excel
      </a>
    </div>
    <div class="tableFilters">
      <input
        class="input"
        type="text"
        v-model="tableData.search"
        placeholder="Search Table"
        @input="getCustomers()"
      />

      <div class="control">
        <div class="select">
          <select v-model="tableData.length" @change="getCustomers()">
            <option
              v-for="(records, index) in perPage"
              :key="index"
              :value="records"
            >
              {{ records }}
            </option>
          </select>
        </div>
      </div>
    </div>
    <div id="report-container">
      <datatable
        :columns="columns"
        :sortKey="sortKey"
        :sortOrders="sortOrders"
        @sort="sortBy"
      >
        <tbody>
          <tr v-for="customer in customers" :key="customer.id">
            <td v-if="columns[0].visible">{{ customer.id }}</td>
            <td v-if="columns[1].visible">{{ customer.name }}</td>
            <td v-if="columns[2].visible">{{ customer.phone }}</td>
          </tr>
        </tbody>
      </datatable>
    </div>
    <pagination
      :pagination="pagination"
      @prev="getCustomers(pagination.prevPageUrl)"
      @next="getCustomers(pagination.nextPageUrl)"
    >
    </pagination>
  </div>
</template>

<script>
import Datatable from "./Datatable.vue";
import Pagination from "./Pagination.vue";
export default {
  components: { datatable: Datatable, pagination: Pagination },
  created() {
    this.getCustomers();
  },
  computed: {
    visibleFields() {
      //console.log(this.columns);
      return this.columns.filter((field) => field.visible);
    },
  },
  data() {
    let sortOrders = {};
    let columns = [
      { width: "33%", label: "ID", name: "id", visible: true },
      { width: "33%", label: "Name", name: "Name", visible: true },
      { width: "33%", label: "Phone", name: "phone", visible: true },
    ];
    columns.forEach((column) => {
      sortOrders[column.name] = -1;
    });
    return {
      customers: [],
      excel_data: [],
      ReportTitle: "report",
      columns: columns,
      sortKey: "deadline",
      sortOrders: sortOrders,
      perPage: ["10", "20", "30"],
      tableData: {
        draw: 0,
        length: 10,
        search: "",
        column: 0,
        dir: "desc",
      },
      pagination: {
        lastPage: "",
        currentPage: "",
        total: "",
        lastPageUrl: "",
        nextPageUrl: "",
        prevPageUrl: "",
        from: "",
        to: "",
      },
    };
  },
  methods: {
    getCustomers(url = "/api/customer") {
      this.tableData.draw++;
      axios
        .get(url, { params: this.tableData })
        .then((response) => {
          let data = response.data;
          if (this.tableData.draw == data.draw) {
            this.customers = data.data.data;
            this.configPagination(data.data);
          }
        })
        .catch((errors) => {
          console.log(errors);
        });
    },
    configPagination(data) {
      this.pagination.lastPage = data.last_page;
      this.pagination.currentPage = data.current_page;
      this.pagination.total = data.total;
      this.pagination.lastPageUrl = data.last_page_url;
      this.pagination.nextPageUrl = data.next_page_url;
      this.pagination.prevPageUrl = data.prev_page_url;
      this.pagination.from = data.from;
      this.pagination.to = data.to;
    },
    sortBy(key) {
      this.sortKey = key;
      this.sortOrders[key] = this.sortOrders[key] * -1;
      this.tableData.column = this.getIndex(this.columns, "name", key);
      this.tableData.dir = this.sortOrders[key] === 1 ? "asc" : "desc";
      this.getCustomers();
    },
    getIndex(array, key, value) {
      return array.findIndex((i) => i[key] == value);
    },
    generateExcel() {
      this.excel_data = document.getElementById("report-container").innerHTML;
      let blob = new Blob([this.excel_data], { type: "application/ms-excel" });
      let link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      link.download = this.ReportTitle + ".xls";
      link.click();
    },
    generateCsv() {
      let csv = [];
      let rows = document.querySelectorAll("table tr");

      for (var i = 0; i < rows.length; i++) {
        var row = [],
          cols = rows[i].querySelectorAll("td, th");

        for (var j = 0; j < cols.length; j++) row.push(cols[j].innerText);

        csv.push(row.join(","));
      }

      // Download CSV file
      this.downloadCSV(csv.join("\n"));
    },
    downloadCSV(csvData) {
      let csv_data = csvData;
      let blob = new Blob([csv_data], {
        type: "text/csv;charset=utf-8",
      });
      let link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      link.download = this.ReportTitle + ".csv";
      link.click();
    },
  },
};
</script>

<style scoped>
.hide-cm {
  margin-bottom: 5px;
}
.download-pg2 {
  float: right;
  font: bold 15px Arial;
  text-decoration: none;
  background-color: #eeeeee;
  color: #0d6efd;
  padding: 5px 9px 4px 8px;
  border-top: 1px solid #cccccc;
  border-right: 1px solid #333333;
  border-bottom: 1px solid #333333;
  border-left: 1px solid #cccccc;
}
.lb {
  padding: 5px;
}
</style>
