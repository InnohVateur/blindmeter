if(document.querySelector(".song") != null){ // If a song has been chosen, then register an onload event

    window.addEventListener("load", (event) => { // When the window loads, set the default visibility for all of the elements

        // If the user is currently granting points and if the play button has been defined, then sets everything to default
        if(document.querySelector(".song").hasAttribute("grantingpoints") && document.querySelector(".startbtn") != null){ 

            // Play Button
            document.querySelector(".startbtn").style.transition = "none";
            document.querySelector(".startbtn").style.visibility = "hidden";
            document.querySelector(".startbtn").style.width = 0;
            document.querySelector(".startbtn").style.height = 0;

            // Time Counter
            document.querySelector(".counter").style.fontSize = "0pt";
            document.querySelector(".counter").style.visibility = "hidden";

            // Answer Form
            document.querySelector(".answer").style.width = "auto";
            document.querySelector(".answer").style.height = "auto";
            document.querySelector(".answer").style.visibility = "visible";

            // Song's Album Cover
            document.querySelector(".answer input[type='image']").style.width = "auto";
            document.querySelector(".answer input[type='image']").style.height = "85vh";
            document.querySelector(".answer input[type='image']").style.visibility = "visible";

            // Song's Title and Artists
            document.querySelector(".answer figcaption").style.width = "auto";
            document.querySelector(".answer figcaption").style.height = "auto";
            document.querySelector(".answer figcaption").style.visibility = "visible";
        }else{
            // See getData function
            getData();
        }
    });
}

const duration = 20; // The duration of the extract in seconds (maximum 30, as the deezer API's preview mp3 are 30 seconds long)

var audioCtx = new AudioContext(); // Creates the song's online audio context to play it
var offCtx = new OfflineAudioContext(1, duration * 44100, 44100); // Creates the song's offline audio context to load it
// Note with offCtx : You can disable it, but not preloading the song can cause sync issues with the time counter depending on the
// user's connection

var source = offCtx.createBufferSource(); // Creates the source

var interv; // Initiates the interv variable to call the time counter's invertal
var tens = 0; // Initiates the tens of seconds left before next second (at the beginning, 0)
var seconds = duration; // Initiates the seconds left before answer (at the beginning, the duration of the extract)

var htmlTens = document.querySelector(".tens"); // Searches for the tens' span through the DOM
var htmlSeconds = document.querySelector(".seconds"); // Searches for the seconds' span through the DOM

/***
getData() : Fetches the mp3 file of the extract of the selected song, and when loaded to the online context, registers the onclick event
for the play button
***/
function getData(){
    var url = document.querySelector(".song").getAttribute("url"); // Searches for the value of the url attribute through the DOM
    // Note : the value is the url to the mp3 file

    var btn = document.querySelector(".startbtn"); // Searches for the play button through the DOM


    fetch(url) // Fetches the file
        .then(data => data.arrayBuffer()) // Converts it to an ArrayBuffer
        .then(arrayBuffer => audioCtx.decodeAudioData(arrayBuffer, (buffer) => { //Decodes data
            source.buffer = buffer; // Sets the buffer of the source
            source.connect(offCtx.destination); // Connects to the offline context
            source.start(); // Starts
            offCtx.startRendering() // Preloads
                .then(renderedBuffer => { // Gets the preloaded buffer
                    btn.style.visibility = "visible"; // Shows the button
                    const song = audioCtx.createBufferSource(); // Creates a buffer source for the online context
                    song.buffer = renderedBuffer; // Sets the buffer
                    song.connect(audioCtx.destination); // Connects to the online context
                    btn.addEventListener("click", event => { // Adds an onclick listener for the play button

                        // Shows and hide elements from the GUI

                        // Play Button
                        document.querySelector(".startbtn").style.transition = "none";
                        document.querySelector(".startbtn").style.visibility = "hidden";
                        document.querySelector(".startbtn").style.width = 0;
                        document.querySelector(".startbtn").style.height = 0;

                        // Time Counter
                        document.querySelector(".counter").style.fontSize = "50pt";
                        document.querySelector(".counter").style.visibility = "visible";


                        if(!btn.hasAttribute("disabled")){// If the button hasn't been clicked yet
                            song.start(audioCtx.currentTime, 0, duration);//Plays the song
                            btn.setAttribute("disabled", "true");// Sets the button to disabled (so the user can't play the extract) several times

                            interv = setInterval(counter, 10);// Starts the time counter
                            song.onended = function() { // When the songs finishes, makes changes to the GUI

                                // Play Button
                                document.querySelector(".startbtn").style.visibility = "hidden";
                                document.querySelector(".startbtn").style.width = 0;
                                document.querySelector(".startbtn").style.height = 0;

                                // Time Counter
                                document.querySelector(".counter").style.fontSize = "0pt";
                                document.querySelector(".counter").style.visibility = "hidden";

                                // Answer Form
                                document.querySelector(".answer").style.width = "auto";
                                document.querySelector(".answer").style.height = "auto";
                                document.querySelector(".answer").style.visibility = "visible";

                                // Song's Album Cover
                                document.querySelector(".answer input[type='image']").style.width = "auto";
                                document.querySelector(".answer input[type='image']").style.height = "85vh";
                                document.querySelector(".answer input[type='image']").style.visibility = "visible";

                                // Song's Title and Artists
                                document.querySelector(".answer figcaption").style.width = "auto";
                                document.querySelector(".answer figcaption").style.height = "auto";
                                document.querySelector(".answer figcaption").style.visibility = "visible";
                            }
                        }
                    });
                });
        }));
}

// Updates the Time Counter
function counter(){
    tens--; // Decreases the tens

    if(tens <= 9){
      htmlTens.innerHTML = "0" + tens; // Displays the one digit's tens
    }

    if (tens > 9){
      htmlTens.innerHTML = tens; // Displays the two digits' tens
    } 
    if (tens < 0) { // If the tens reach zero
      seconds--; // Decreases the seconds
      htmlSeconds.innerHTML = "0" + seconds; // First displays the last digit of the seconds
      tens = 99; // Sets the tens to the max
      htmlTens.innerHTML = 99; // Render the tens' changes
    }
    if (seconds > 9){ // If the seconds are two digits
      htmlSeconds.innerHTML = seconds; // Displays the two digits of the seconds
    }
    if(seconds == 0 && tens == 0){ // If the counter ends
        clearInterval(interv); // Stops the interval
    }
}