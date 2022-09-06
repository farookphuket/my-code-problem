import Link from "next/link";

export const Header = () => {
  return (
    <header className="w-full px-6 z-50 top-0 sticky bg-blue-700">
      <nav className="flex justify-between">
        <div>
          <h1 className="text-3xl text-blue-500 py-4">the header</h1>
        </div>
        <div className="block py-6 md:hidden">mb menu</div>
        <ul className="hidden md:flex md:justify-end md:py-6 gap-4 pr-2">
          <li>
            <Link href="/">
              <a href="">
                <span>i</span>
                <span>Home</span>
              </a>
            </Link>
          </li>
          <li>post</li>
          <li>about</li>
        </ul>
      </nav>
    </header>
  );
};

export default Header;
