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

      <?php if($error) { ?>

      <b-alert 
        variant="danger" 
        dismissible
        show><?= $error ?></b-alert>

      <?php } ?>

      <!-- preview pane -->
      <div v-show="previewMode" class="problem-item">
        <div class="row justify-content-left">
          <div class="col mb-3">
            <h3>Preview</h3>
            <h5>
              <span v-if="newItem.name.trim()">by {{ newItem.name }}</span>
              <span v-if="newItem.email.trim()">
                ( <a :href="'mailto:' + newItem.email">{{ newItem.email }}</a> )
              </span>
            </h5>
          </div>
        </div>

        <div class="row justify-content-left">
          <div class="col col-picture" v-if="newItem.file">
            <img :src="imagePreview" class="problem-image" />
          </div>

          <div class="col">
            <p>
              {{ newItem.text }}
            </p>            
          </div>
        </div>
      </div>
      <!-- /preview pane -->

      <!-- form -->
      <b-form 
        enctype="multipart/form-data" 
        method="post" 
        action="/problem/create"
        @submit="beforeSubmit"
        id="problem-form">
        <b-row class="mb-4">
          <b-col sm="6" offset="3">
            <h3 >New problem</h3>
          </b-col>
        </b-row>
        <b-row class="mb-2">
          <b-col sm="3" class="text-right"><label for="f-name">Name</label></b-col>
          <b-col sm="6">
            <b-input 
              id="f-name" 
              name="name" 
              type="text" 
              v-model="newItem.name" 
              required></b-input>
          </b-col>
        </b-row>
        <b-row class="mb-2">
          <b-col sm="3" class="text-right"><label for="f-email">Email</label></b-col>
          <b-col sm="6">
            <b-input 
              id="f-email" 
              name="email" 
              type="email" 
              v-model="newItem.email" 
              required></b-input>
          </b-col>
        </b-row>
        <b-row class="mb-2">
          <b-col sm="3" class="text-right"><label for="f-text">Text</label></b-col>
          <b-col sm="6">
            <b-textarea 
              id="f-text" 
              name="text" 
              v-model="newItem.text" 
              :rows="5" 
              required></b-textarea>
          </b-col>
        </b-row>
        <b-row class="mb-4">
          <b-col sm="3" class="text-right"><label for="f-image">Image</label></b-col>
          <b-col sm="5">
            <b-file 
              id="f-image" 
              name="image"
              accept="image/jpeg, image/png, image/gif"
              ref="fileinput"
              @change="fileSelected"
              ></b-file>
          </b-col>
          <b-col sm="1">
            <b-button @click="clearFiles">Clear</b-button>
          </b-col>
        </b-row>
        <b-row class="mb-2">
          <b-col sm="6" offset="3">
            <b-button variant="success" @click="togglePreviewMode">
              {{ previewMode ? 'Hide preview' : 'Show preview' }}
            </b-button>
            <b-button type="submit" variant="primary">Create</b-button>
            <b-button variant="secondary" href="/">Cancel</b-button>
          </b-col>
        </b-row>
      </b-form>
      <!-- /form -->

    </main><!-- /.container -->

    <script src="/js/app.js"></script>
    <script type="text/javascript">
      var app = new Vue({
        el: '#vue-app',

        data: {
          newItem: {
            name: '<?= str_replace("'", "\'", $name) ?>',
            email: '<?= str_replace("'", "\'", $email) ?>',
            text: '<?= str_replace("'", "\'", $text) ?>',
            file: null,
          },
          previewMode: false,
          exitLock: true,

          imagePreview: ''
        },

        created: function() {
          window.onbeforeunload = this.beforeLeaving;
        },

        methods: {

          beforeSubmit: function() {
            this.exitLock = false;
          },

          beforeLeaving: function(e) {
            if(this.exitLock &&
              (this.newItem.name != '' || 
               this.newItem.email != '' || 
               this.newItem.text != '')) {

              return 'There are unsaved changes. Are you sure you want to leave?';
            }
          },

          togglePreviewMode: function() {
            this.previewMode = !this.previewMode;
          },

          clearFiles: function() {
            this.$refs.fileinput.reset();
          },

          fileSelected: function() {
            this.newItem.file = document.getElementById('f-image').files[0];

            if(this.newItem.file) {
              if ( /\.(jpe?g|png|gif)$/i.test( this.newItem.file.name ) ) {
                let reader = new FileReader();
                reader.addEventListener("load", function () {
                  this.imagePreview = reader.result;
                }.bind(this), false);

                reader.readAsDataURL( this.newItem.file );
              }
            }
          }

        }
      });
    </script>
  </body>
</html>