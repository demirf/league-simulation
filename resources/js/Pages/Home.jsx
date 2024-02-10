import React, { useState } from "react";
import Layout from "../Components/Layout.jsx";

const Home = ({ teams }) => {
    return (
        <>
            <Layout>
                <p className={"text-3xl font-medium mb-2 text-center"}>Teams</p>
                <div className={"flex flex-col gap-3"}>
                    {teams.map((team, index) => <div className={"flex items-center gap-2 border-b border-gray-100 py-3"}>
                        <p className={"w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-700"}>{index + 1}</p>
                        <div>
                            <img src={team.logo_url} className={"w-12 h-12"} />
                        </div>
                        <p>{team.name}</p>
                    </div>)}
                </div>

            </Layout>
        </>
    )
}

export default Home;
