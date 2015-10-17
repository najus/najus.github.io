$(document).ready(function() {
    $('#changedContent').load('web/aboutme.html');
    $('#Academic').bind('click', function() {
        $('li').removeClass('active');
        $('#Academic').addClass('active');
        $('#changedContent').load('web/academic.html');
    });
    $('#Technical').bind('click', function() {
        $('li').removeClass('active');
        $('#Technical').addClass('active');
        $('#changedContent').load('web/technical.html');
    });
	$('#bcds').bind('click', function() {
        $('li').removeClass('active');
        $('#bcds').addClass('active');
        $('#changedContent').load('web/bcds.html');
    });
	$('#8puzzle').bind('click', function() {
        $('li').removeClass('active');
        $('#8puzzle').addClass('active');
        $('#changedContent').load('web/8puzzle.html');
    });
	$('#tictactoe').bind('click', function() {
        $('li').removeClass('active');
        $('#tictactoe').addClass('active');
        $('#changedContent').load('web/tictactoe.html');
    });
	$('#ibm').bind('click', function() {
        $('li').removeClass('active');
        $('#ibm').addClass('active');
        $('#changedContent').load('web/ibm.html');
    });
	$('#android').bind('click', function() {
        $('li').removeClass('active');
        $('#android').addClass('active');
        $('#changedContent').load('web/android.html');
    });
	$('#music').bind('click', function() {
        $('li').removeClass('active');
        $('#music').addClass('active');
        $('#changedContent').load('web/music.html');
    });
	$('#pc-games').bind('click', function() {
        $('li').removeClass('active');
        $('#pc-games').addClass('active');
        $('#changedContent').load('web/pc-games.html');
    });
	$('#social-psychology').bind('click', function() {
        $('li').removeClass('active');
        $('#social-psychology').addClass('active');
        $('#changedContent').load('web/social-psychology.html');
    });
    $('#ExtraCurricular').bind('click', function() {
        $('li').removeClass('active');
        $('#ExtraCurricular').addClass('active');
        $('#changedContent').load('web/extracurricular.html');
    });
    $('#AboutMe').bind('click', function() {
        $('li').removeClass('active');
        $('#AboutMe').addClass('active');
        $('#changedContent').load('web/aboutme.html');
    });
    $('#ProfessionalExperience').bind('click', function() {
        $('li').removeClass('active');
        $('#ProfessionalExperience').addClass('active');
        $('#changedContent').load('web/professionalexperience.html');
    });
});