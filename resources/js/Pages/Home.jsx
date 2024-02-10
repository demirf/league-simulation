import React from "react";
import Layout from "../Components/Layout.jsx";
import {Button} from "../Components/Button.jsx";

const Home = ({ teams }) => {
    return (
        <>
            <Layout>
                <p className={"text-3xl font-medium mb-4 text-center"}>Teams</p>
                <div className={"flex flex-col gap-3"}>
                    {teams.map((team, index) => <div key={index} className={"flex items-center gap-2 border-b border-gray-100 py-3"}>
                        <p className={"w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-700"}>{index + 1}</p>
                        <div>
                            <img src={team.logo_url} className={"w-12 h-12"} />
                        </div>
                        <p>{team.name}</p>
                    </div>)}
                </div>
                <div className={"text-center mt-4"}>
                    <Button>Start Tournament</Button>
                </div>
            </Layout>
        </>
    )
}

export default Home;
