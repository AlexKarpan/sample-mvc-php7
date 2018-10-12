<!doctype html>
<html lang="en" class="guest">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>BeeJee sample application</title>

    <link href="/vendor/bootstrap.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
  </head>

  <body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="/">Problems</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav ml-auto">

          <?php if($this->auth->isGuest()) { ?>

            <li class="nav-item active">
              <a class="nav-link" href="/login">Log in</a>
            </li>

          <?php } else { ?>

            <li class="nav-item active">
              <a class="nav-link" href="/admin">Admin zone</a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="/logout">Log out</a>
            </li>

          <?php } ?>

        </ul>
      </div>
    </nav>

    <main role="main" class="container" id="vue-app" v-cloak>

      <div class="row mb-2">
        <b-col sm="6">
          <b-button variant="primary" href="/problem/create">Add a problem</b-button>
        </b-col>
        <b-col sm="3" offset="3" class="text-right">
          <b-select 
            v-model="sort" 
            :options="sortOptions" 
            class="mb-3" 
            size="sm" 
            @input="sortingChanged"
            />
        </b-col>
      </div>

      <div class="problem-item" v-for="item in items">
        <div class="row justify-content-left">
          <div class="col mb-3">
            <h3>
              Problem #{{ item.id }}
            </h3>
            <h5>by {{ item.name }} ( <a :href="'mailto:' + item.email">{{ item.email }}</a> )</h5>
          </div>
          <div class="col text-right">
            <h3 v-if="item.is_completed == 1" class="is-completed text-success">
                &#x2713; Solved!
            </h3>
          </div>
        </div>

        <div class="row justify-content-left">
          <div class="col col-picture" v-if="item.image">
            <div class="problem-image">
              <img :src="item.image" />
            </div>
          </div>

          <div class="col">
            <p>
              {{ item.text }}
            </p>            
          </div>
        </div>
      </div>

      <div class="mt-6" v-show="perPage < totalCount">
        <b-pagination 
          size="md" 
          align="right" 
          :total-rows="totalCount"
          v-model="currentPage" 
          :per-page="perPage"
          @input="pageClicked">
        </b-pagination>
      </div>

    </main><!-- /.container -->

    <script src="/js/app.js"></script>
    <script type="text/javascript">
      var app = new Vue({
        el: '#vue-app',

        data: {
          perPage: <?= $problemsPerPage ?>,
          currentPage: 1,
          totalCount: 0,
          sort: 'u',
          items: [],

          sortOptions: [
            { value: 'u', text: 'Not sorted' },
            { value: 'na', text: 'Sort by name: A to Z' },
            { value: 'nd', text: 'Sort by name: Z to A' },
            { value: 'ea', text: 'Sort by email: A to Z' },
            { value: 'ed', text: 'Sort by email: Z to A' },
            { value: 's0', text: 'Sort by status: incomplete first' },
            { value: 's1', text: 'Sort by status: completed first' }
          ]
        },

        created: function () {
          var sort = this.$cookie.get('sort');
          var modes = this.sortOptions.map(o => o.value);
          if(modes.includes(sort)) {
            this.sort = sort;
          }

          this.loadData();
        },

        methods: {
          loadData: function() {
            var that = this;
            axios.get('/problem/data/' + ((this.currentPage - 1) * this.perPage) + 
                '/' + this.perPage + '/' + this.sort)
              .then(function (response) {
                that.totalCount = response.data.count;
                that.items = response.data.items;
                if(that.totalCount < (that.currentPage - 1) * that.perPage) {
                  that.pageClicked(1);
                }
              });
          },

          pageClicked: function(page) {
            this.currentPage = page;
            this.loadData();
          },

          sortingChanged: function() {
            this.$cookie.set('sort', this.sort);
            this.loadData();
          }

        }
      });
    </script>
  </body>
</html>