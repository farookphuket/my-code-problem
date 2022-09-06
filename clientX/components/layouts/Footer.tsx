import { useEffect } from "react";
import { useVisitor } from "../../hooks/visitor";

export const Footer = () => {
  const { visitor, getVisitor } = useVisitor();
  const v = visitor;
  useEffect(() => {
    getVisitor();
  }, []);
  //console.log(v);
  return (
    <footer className="flex justify-center items-center">
      <div className="block md:flex md:justify-between md:gap-3">
        <div className="text-center">
          <p className="footerLabel">this year</p>
          <p className="footerText">{v.visited_year}</p>
        </div>
        <div className="text-center">
          <p className="footerLabel">this month</p>
          <p className="footerText">{v.visited_month}</p>
        </div>
        <div className="text-center">
          <p className="footerLabel">today</p>
          <p className="footerText">{v.visited_today}</p>
        </div>
        <div className="ml-6 text-center">
          <p className="footerLabel">You</p>

          <ul className="block md:flex md:justify-between md:gap-2">
            <li>
              <span className="footerLabel">ip</span>
              <span className="footerText">{v.ip}</span>
            </li>
            <li>
              <span className="footerLabel">os</span>
              <span className="footerText">{v.os}</span>
            </li>
            <li>
              <span className="footerLabel">Browser</span>
              <span className="footerText">{v.browser}</span>
            </li>
          </ul>
        </div>
        <div className="text-center">
          <p className="footerLabel">contact</p>
          <ul className="block md:flex md:justify-between md:gap-2">
            <li>
              <span className="footerLabel">tel</span>
              <span className="footerText">
                {process.env.NEXT_PUBLIC_CONTACT_PHONE}
              </span>
            </li>

            <li>
              <span className="footerLabel">email</span>
              <span className="footerText">
                {process.env.NEXT_PUBLIC_CONTACT_EMAIL}
              </span>
            </li>
          </ul>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
