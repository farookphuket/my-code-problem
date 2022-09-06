import Head from "next/head";
import Header from "./Header";
import Footer from "./Footer";
import { useAuth } from "../../hooks/auth";
interface MemberLayoutProps {
  title: string;
}

export const MemberLayout: React.FC<MemberLayoutProps> = ({
  title,
  children,
}) => {
  const { user } = useAuth({ middleware: "auth" });
  return (
    <>
      <Head>
        <title>{title}</title>
      </Head>
      <div className="wrapper">
        <Header />
        <main className="min-h-screen">
          {children}
          {user}
        </main>
        <Footer />
      </div>
    </>
  );
};

export default MemberLayout;
