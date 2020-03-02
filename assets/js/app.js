/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
import '../css/app.css';

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');



console.log('Hello Webpack Encore!!!!!!! Edit me in assets/js/app.js');


// fontawesome
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

//----------- add and delete fields in formVideo----////

var $collectionHolder;

// setup an "add a video" link
var $addVideoButton = $('<button type="button" class="add_video_link">Add a video</button>');
var $newLinkLi = $('<li></li>').append($addVideoButton);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of videos
    $collectionHolder = $('ul.videos');

    // add a delete link to all of the existing tag form li elements
    $collectionHolder.find('li').each(function() {
        addVideoFormDeleteLink($(this));
    });
    // add the "add a video" anchor and li to the videos ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addVideoButton.on('click', function(e) {
        // add a new video form (see next code block)
        addVideoForm($collectionHolder, $newLinkLi);
    });
});


function addVideoForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your videos field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a video" link li
    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);

    // add a delete link to the new form
    addVideoFormDeleteLink($newFormLi);
}

function addVideoFormDeleteLink($videoFormLi) {
    var $removeFormButton = $('<button type="button">Delete this video</button>');
    $videoFormLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // remove the li for the video form
        $videoFormLi.remove();
    });
}


// button "load more" on small screen

$( ".display-button" ).click(function() {
    $( ".container.grid-img" ).css('display', 'block'); // On affiche les tricks
    $( ".display-button" ).css('display', 'none'); // On cache le bouton
  });