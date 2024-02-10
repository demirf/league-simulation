import {Link} from "@inertiajs/inertia-react";

const Layout = ({children}) => {
    return (
        <>
            <header className="bg-white shadow">
                <nav className="mx-auto flex max-w-7xl items-center p-6 lg:px-4" aria-label="Global">
                    <div className="flex">
                        <Link href="/" className={"text-lg font-medium"}>
                            Home
                        </Link>
                    </div>
                </nav>
            </header>
            <div className={"mt-20 max-w-7xl p-6 lg:px-4 mx-auto"}>
                {children}
            </div>
        </>
    )
}

export default Layout;
