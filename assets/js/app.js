/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
import "../css/app.css";

// lightbox2
require("lightbox2/dist/css/lightbox.css");
require("lightbox2/dist/js/lightbox-plus-jquery.js");

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require("jquery");
import axios from "axios";

// fontawesome

require("@fortawesome/fontawesome-free/css/all.min.css");
require("@fortawesome/fontawesome-free/js/all.js");

//----------- add and delete fields in formVideo----////

var $collectionHolder;

// setup an "add a video" link
var $addVideoButton = $('<button type="button" class="add_video_link btn btn-primary mt-4">Ajouter une nouvelle vidéo</button>');
var $newLinkLi = $('<li class="test"></li>').append($addVideoButton);

jQuery(document).ready(function () {
    // Get the ul that holds the collection of videos
    $collectionHolder = $("ul.videos");

    // add a delete link to all of the existing tag form li elements
    /*$collectionHolder.find('li.').each(function () {
        addVideoFormDeleteLink($(this));
    });*/

    // add the "add a video" anchor and li to the videos ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data("index", $collectionHolder.find(":input").length);

    $addVideoButton.on("click", function (e) {
        // add a new video form (see next code block)
        addVideoForm($collectionHolder, $newLinkLi);
    });
});


function addVideoForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data("prototype");

    // get the new index
    var index = $collectionHolder.data("index");

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your videos field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data("index", index + 1);

    // Display the form in the page in an li, before the "Add a video" link li
    var $newFormLi = $("<li></li>").append(newForm);
    $newLinkLi.before($newFormLi);

    // add a delete link to the new form
    addVideoFormDeleteLink($newFormLi);
}

function addVideoFormDeleteLink($videoFormLi) {
    var $removeFormButton = $('<button type="button" class="btn btn-danger">Supprimer le champs</button>');
    $videoFormLi.append($removeFormButton);

    $removeFormButton.on("click", function (e) {
        // remove the li for the video form
        $videoFormLi.remove();
    });
}
//--------top button---------------------

//Get the button:
$("#topScroll").click(function (Event) {
    Event.preventDefault();
    topFunction();
});

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}

//------ button "view more" on small screen---------

$(".display-button").click(function () {
    $("#tricksimages").css('display', 'block'); // On affiche les tricks
    $(".display-button").css('display', 'none'); // On cache le bouton
});

//---------- button "load more comments"--------------------
var click = 0;
var CountNumber = $('#comments').data('allcomments');
var start = 0;

//if there is no more comments to load we hide the button
function lastClick() {
    if (start > CountNumber) {
        $('#loadMoreComments').hide();
    }
}
//the path  to the comment view
var path = $('#comments').data('path');

function loadMoreComments(event) {
    event.preventDefault();
    click++;
    start = 5 * click;
    const url = path + start;
    axios.get(url).then(function (response) {
        jQuery("#comments").append(response.data);
    });
    lastClick();
}
document.getElementById("loadMoreComments").addEventListener("click", loadMoreComments);