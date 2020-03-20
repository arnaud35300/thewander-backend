import React from 'react';
import Sound from 'react-sound';

// == Local import
// import StyledAnim404 from './StyledAnim404'
import background from 'src/assets/simple-background.gif';
import spaceship from 'src/assets/404/rockets_PNG13285.png';
import earth from 'src/assets/404/earth.png';
import moon from 'src/assets/404/moon_PNG52.png';
import astronaut from 'src/assets/404/astronaut_PNG44.png';
import './style.css'

// TO FIX: used local mp3 -> webpack loader is not working


// == Component
const Anim404 = (volume, isPlaying) => {
  const play = isPlaying ? 'PLAYING' : 'STOPPED';
  return (
  <>
    <Sound
        url="https://ajna-design.fr/wp-content/uploads/2020/03/The-Wander-Loop-Kinomood_-_Bring_Me_Over.mp3"
        playStatus={Sound.status[play]}
        // volume={volume}
        volume="100"
        playFromPosition="1500"
        autoload="false"
      />
    <section className="bg-purple">
    <img src={background} id="background"/>
      <div className="stars">
        <div className="central-body">
          <img className="image-404" src="http://salehriaz.com/404Page/img/404.svg"/>
          <a href="/home" className="btn-go-home">Go back to earth</a>
        </div>
        <div className="objects">
          <img className="object_rocket" src={spaceship} width="100px"/>
            <div className="earth-moon">
              <img className="object_earth" src={earth} width="120px"/>
              <img className="object_moon" src={moon} width="30px"/>
            </div>
            <div className="box_astronaut">
              <img className="object_astronaut" src={astronaut} width="250px"/>
            </div>
          </div>
          <div className="glowing_stars">
            <div className="star"></div>
            <div className="star"></div>
            <div className="star"></div>
            <div className="star"></div>
            <div className="star"></div>
          </div>
        </div>
        
      </section>
    </>
  );
};
// == Export
export default Anim404;
