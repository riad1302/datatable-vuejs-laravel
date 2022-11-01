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
  },
};
</script>

<style scoped>
.hide-cm {
  margin-bottom: 5px;
}
.lb {
  padding: 5px;
}
</style>
