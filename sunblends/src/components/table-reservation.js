import React from "react";
import bgImg from "../assets/images/table-leaves-shape.png";
import titleShape from "../assets/images/title-shape.svg";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faEnvelope } from "@fortawesome/free-solid-svg-icons";

const BookTableSection = () => {
  return (
    <section className="book-table section bg-light">
      <div className="book-table-shape">
        <img src={bgImg} alt="Table Leaves Shape" />
      </div>
      <div className="book-table-shape book-table-shape2">
        <img src={bgImg} alt="Table Leaves Shape" />
      </div>

      <div className="sec-wp">
        <div className="container">
          <div className="row">
            <div className="col-lg-12">
              <div className="sec-title text-center mb-5">
                <p className="sec-sub-title mb-3">Book Table</p>
                <h2 className="h2-title">Opening Table</h2>
                <div className="sec-title-shape mb-4">
                  <img src={titleShape} alt="Title Shape" />
                </div>
              </div>
            </div>
          </div>

          <div className="book-table-info">
            <div className="row align-items-center">
              <div className="col-lg-4 info">
                <div className="table-title table-title-1 text-center">
                  <h3>Monday to Friday</h3>
                  <p>7:00 am - 5:00 pm</p>
                </div>
              </div>
              <div className="col-lg-4 info">
                <div className="call-now text-center">
                  <a href="mailto:thesunblendscafe.ph@gmail.com">
                    <FontAwesomeIcon icon={faEnvelope} />
                  </a>
                  <h5>thesunblendscafe.ph@gmail.com</h5>
                </div>
              </div>
              <div className="col-lg-4 info">
                <div className="table-title table-title-2 text-center">
                  <h3>Saturday to Sunday</h3>
                  <p>CLOSE</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default BookTableSection;
