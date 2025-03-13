import React from "react";
import PromoImg1 from "../assets/images/Peppermint Mocha Espresso Martini.png";
import PromoImg2 from "../assets/images/Vanilla Ice Cream.png";

const Promo = () => {
  return (
    <>
      <section className="two-col-sec section">
        <div className="container">
          <div className="row align-items-center">
            <div className="col-lg-5">
              <div className="sec-img mt-5">
                <img
                  className="img-promo"
                  src={PromoImg1}
                  alt="Peppermint Mocha Espresso Martini"
                />
              </div>
            </div>
            <div className="col-lg-7">
              <div className="sec-text text1">
                <h2 className="xxl-title promo-title">
                  Peppermint Mocha Espresso Martini
                </h2>
                <p>
                  This is Lorem ipsum dolor sit amet consectetur adipisicing
                  elit. Amet dolores eligendi earum eveniet soluta officiis
                  asperiores repellat, eum praesentium nihil totam. Non ipsa
                  expedita repellat atque mollitia praesentium assumenda quo
                  distinctio excepturi nobis tenetur, cum ab vitae fugiat hic
                  aspernatur? Quos laboriosam, repudiandae exercitationem atque
                  a excepturi vel. Voluptas, ipsa.
                </p>
                <p>
                  This is Lorem ipsum dolor sit amet consectetur adipisicing
                  elit. At fugit laborum voluptas magnam sed ad illum? Minus
                  officiis quod deserunt.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="two-col-sec section pt-0">
        <div className="container">
          <div className="row align-items-center">
            <div className="col-lg-6 order-lg-1 order-2">
              <div className="sec-text text2">
                <h2 className="xxl-title promo-title">Vanilla Ice Cream</h2>
                <p>
                  This is Lorem ipsum dolor sit amet consectetur adipisicing
                  elit. Amet dolores eligendi earum eveniet soluta officiis
                  asperiores repellat, eum praesentium nihil totam. Non ipsa
                  expedita repellat atque mollitia praesentium assumenda quo
                  distinctio excepturi nobis tenetur, cum ab vitae fugiat hic
                  aspernatur? Quos laboriosam, repudiandae exercitationem atque
                  a excepturi vel. Voluptas, ipsa.
                </p>
                <p>
                  This is Lorem ipsum dolor sit amet consectetur adipisicing
                  elit. At fugit laborum voluptas magnam sed ad illum? Minus
                  officiis quod deserunt.
                </p>
              </div>
            </div>
            <div className="col-lg-6 order-lg-2 order-1">
              <div className="sec-img ">
                <img
                  className="img-promo"
                  src={PromoImg2}
                  alt="Vanilla Ice Cream"
                />
              </div>
            </div>
          </div>
        </div>
      </section>
    </>
  );
};

export default Promo;
