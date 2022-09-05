# Easy Videos WordPress plugin
This is a WordPress docker instance that hosts the Easy Videos plugin. This plugin fetches data from Linus Tech Tips via the Youtube API and creates a custom post type called Easy Videos + its required meta fields. The "Add New" button of the Easy Video admin page is changed to "Insert Video" and instead of redirecting to the create a new post page, it fetches the data via the youtube api and it creates a new post automatically. 

---
<h2>How to Install & Use:</h2>
<ol>
<li>Install Docker if you don't have it already.</li>
<li>Download the entire project as ZIP.</li>
<li>Unzip the project to a folder of your choice.</li>
<li>Open the terminal in that folder and type "docker-compose up" and wait for the dependencies to be downloaded.</li>
<li>Once the dependencies are downloaded go to http://localhost:8992/wp-admin.</li>
<li>Log in with the following credentials ID = wordpress Password = wordpress.</li>
<li>Activate the Easy Videos pluin if its not already activated.</li>
<li>Go to the Easy Videos custom post type in the left admin pannel.</li>
<li>Click on the "Insert Video" button and wait for the script to execute, once its done a new post should be created with the data from the Youtube API.</li>
</ol>

<h2>Project structure:</h2>
<ul>
<li>📁db_data (responsible for database persistance)</li>
<li>📁wordpress (main WordPress folder)
<ul>
<li>📁wp-content
<ul>
<li>📁plugins
<ul>
<li>📁easy-videos</li>
</ul>
</li>
</ul>
</li>
</ul>
</li>
<li>📄docker-compose.yaml</li>
<li>📄Dockerfile</li>
<li>📄README.MD</li>
</ul>
<h2>Plugin structure (easy-videos folder):</h2>
<ul>
  <li>📁classes
    <ul>
      <li>📄PluginController.php</li>
      <li>📄Utility.php</li>
      <li>📄Video.php</li>
      <li>📄YoutubeAPI.php</li>
    </ul>
  </li>
  <li>📁css</li>
  <li>📁js
    <ul>
      <li>📄script.js</li>
    </ul>
  </li>
  <li>📄easy-videos.php</li>
</ul>
<h3>How the plugin works:</h3>
<p>The easy-videos.php in the root folder is the entry point to the plugin, it registers the plugin and initializes the PluginController class.In the PluginController the init method changes the "Add New" button url to an endpoint that we will create later in the next step. At the same time the PluginController also creates a Video instance and calls its register and initRestApiEndpoint methods. In the Video class we register the Easy Videos custom post type and the required meta fields + the saveVideo method used to create a post with the Youtube API data. In the saveVideo method we instantiate a YoutubeAPI object and fetch the data from the youtube api, after the data is fetched we check to see if we already have the fetched data in the db, if we do we will fetch until we find new data, if we dont we format the data and pass it to the wp_insert_post function in order to create a new post.</p>
<h2>How to fetch the videos on the front end:</h2>
<p>In order to fetch the videos on the Front End, you can use the script down bellow.</p>

```php
<?php
$args = array( 
    'post_type' => 'easy-videos',
    'numberposts'  => -1
);

$videos = get_posts( $args );

foreach($videos as $video){?>
<iframe id="player" type="text/html" width="640" height="390"
src="http://www.youtube.com/embed/<?php echo get_post_meta($video->ID, 'youtubeIDMetaField', true) ?>?enablejsapi=1"
frameborder="0"></iframe>

