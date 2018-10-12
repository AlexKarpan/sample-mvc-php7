<!doctype html>
<html lang="en" class="admin">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>BeeJee sample application: admin zone</title>

    <link href="/vendor/bootstrap.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
  </head>

  <body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="/admin">Admin zone</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active">
            <a class="nav-link" href="/">Visit website</a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="/logout">Log out</a>
          </li>
        </ul>
      </div>
    </nav>

    <main role="main" class="container" id="vue-app" v-cloak>

    	<b-alert 
    		variant="success" 
    		dismissible 
    		:show="alertCountdown">{{ message }}</b-alert>

			<b-table 
				show-empty
				striped 
				:per-page="10"
				:fields="fields"			
				:items="items"
			>
				<template slot="image" slot-scope="data">
		      <img :src="data.item.image" class="problem-image" v-if="data.item.image" />
		    </template>
					
				<template slot="is_completed" slot-scope="data">
		      <span class="text-success" v-if="data.item.is_completed == 1">Yes</span>
		    </template>

		    <template slot="actions" slot-scope="row">
	        <!-- We use @click.stop here to prevent a 'row-clicked' event from also happening -->
	        <b-button size="sm" variant="primary" @click.stop="row.toggleDetails">
	          {{ row.detailsShowing ? 'Hide' : 'Edit text' }}
	        </b-button>
	        <b-button size="sm" variant="success" @click.stop="toggleStatus(row.item)" class="mr-1">
	          {{ row.item.is_completed == '1' ? 'Set unsolved' : 'Set solved' }}
	        </b-button>
	      </template>

	      <template slot="row-details" slot-scope="row">
	      	<b-row align-v="end">
		      	<b-col sm="9">
			        <b-textarea 
		            name="text"
		            v-model="row.item.text"
		            :rows="5"
		            required></b-textarea>
	          </b-col>
	          <b-col sm="3">
	          	<b-button size="sm" variant="primary" @click="updateText(row)">
	          		Update
	        		</b-button>
        		</b-col>
      		</b-row>
	      </template>

			</b-table>

    </main><!-- /.container -->

    <script src="/js/app.js"></script>
    <script type="text/javascript">
      var app = new Vue({
        el: '#vue-app',

        data: {
        	message: '',
        	alertCountdown: 0,
          items: [],
          fields: [ 
          	'id', 
          	'image', 
          	'name', 
          	'email', 
          	{ key: 'is_completed', label: 'Solved?' },
          	{ key: 'actions', label: 'Actions' }
        	]
        },

        created: function () {
          this.loadData();
        },

        methods: {
          loadData: function() {
            var that = this;
            axios.get('/problem/data/0/0/u')
              .then(function (response) {
                that.items = response.data.items;
              });
          },

          toggleStatus: function(item) {
            axios.post('/admin/problem/toggle-status', { id: item.id })
              .then(function (response) {
              	item.is_completed = 1 - item.is_completed;              	
              });
          },

          updateText: function(row) {
          	var that = this;
          	axios.post('/admin/problem/update-text', { 
          			id: row.item.id, 
          			text: row.item.text 
          		})
              .then(function (response) {
              	that.message = 'The record was updated.';
              	that.alertCountdown = 3;
              	row.toggleDetails();
              });
          }

        }
      });
    </script>
  </body>
</html>