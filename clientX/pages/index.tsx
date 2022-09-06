import type { NextPage } from "next";
import GuestLayout from "../components/layouts/GuestLayout";
const Home: NextPage = () => {
  return (
    <GuestLayout title="this is the index page">
      <div>
        <h1 className="text-green-400 border">this is the first run</h1>
        <p>this is the home body</p>
      </div>
    </GuestLayout>
  );
};

export default Home;
