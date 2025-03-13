import React from "react";
import titleShape from "../assets/images/title-shape.svg";
import SunblendsStaff from "../assets/images/Sundblends-img.jpg";

const AboutSection = () => {
  return (
    <section className="about-sec section" id="about">
      <div className="container">
        <div className="row">
          <div className="col-lg-12">
            <div className="sec-title text-center mb-5">
              <p className="sec-sub-title mb-3">About Us</p>
              <h2 className="h2-title">
                Discover our <span>restaurant story</span>
              </h2>
              <div className="sec-title-shape mb-4">
                <img src={titleShape} alt="Title Shape" />
              </div>
              <p className="about-text">
                The SunBlends Café was launched in December 2019, with its first
                branch in Montalban, Rizal. It's a dream project of a mother and
                daughter who loves coffee and tea, respectively. The project
                came into fruition as a tribute after the daughter's death in
                2018. This is a way of celebrating a dear loved one's memories
                alive. The family dedicated the café in remembrance and
                reflection of the love, light, and laughter she brought to this
                world and most of all, the life she rendered in service for the
                Son, Jesus Christ. In 2022, the second branch in Quezon City
                opened right in the heart of Trinity University of Asia. In
                partnership with the College of Hospitality and Tourism
                Management, SunBlends Café serves as an incubation for students
                for their hands-on experience on food and beverage preparation,
                order taking, service and delivery, events and catering, and
                most importantly the value of practicing good customer service.
              </p>
            </div>
          </div>
        </div>
      </div>
      <div className="row">
        <div className="col-lg-8 m-auto">
          <div className="about-video">
            <div
              className="about-video-img"
              style={{ backgroundImage: `url(${SunblendsStaff})` }}
            ></div>
            {/* Uncomment and use the code below if you need the video functionality */}
            {/* <div className="play-btn-wp">
              <a href="assets/images/video.mp4" data-fancybox="video" className="play-btn">
                <i className="uil uil-play"></i>
              </a>
              <span>Watch The Recipe</span>
            </div> */}
          </div>
        </div>
      </div>
    </section>
  );
};

export default AboutSection;
