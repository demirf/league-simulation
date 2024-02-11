import Layout from "../Components/Layout.jsx";
import {Button} from "../Components/Button.jsx";

const AllMatches = ({ allMatches }) => {
    return (
        <Layout>
            <div className={"grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-3 xl:gap-x-8"}>
                {allMatches.map((matches, index) => <div key={index} className={"flex flex-col items-start gap-4 border border-gray-100 rounded-lg relative"}>
                    <div className={"bg-gray-800 w-full p-4 rounded-lg rounded-bl-none rounded-br-none"}>
                        <span className={"text-white text-lg font-medium"}>{index + 1} week</span>
                    </div>
                    {matches.map((match, matchIndex) => <div key={matchIndex} className={"flex justify-start gap-4 p-4"}>
                        <img src={match.home_team_logo} alt={"home team logo"} className={"w-6 h-6"} />
                        <span>{match.home_team}</span>
                        -
                        <img src={match.away_team_logo} alt="away team logo" className={"w-6 h-6"} />
                        <span>{match.away_team}</span>
                    </div>)}
                </div>)}
            </div>
            <div className={"flex justify-center mt-4"}>
                <Button>Start Simulation</Button>
            </div>
        </Layout>
    )
}

export default AllMatches;
