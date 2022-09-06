import Head from "next/head";
import Header from "./Header";
import Footer from "./Footer";
export interface GuestLayoutProps {
  title: string;
}
export const GuestLayout: React.FC<GuestLayoutProps> = ({
  title,
  children,
}) => {
  return (
    <>
      <Head>
        <title>
          {title} | {process.env.NEXT_PUBLIC_APP_TITLE}
        </title>
      </Head>
      <div className="wrapper">
        <Header />
        <main className="min-h-screen">{children}</main>
        <Footer></Footer>
      </div>
    </>
  );
};

export default GuestLayout;
