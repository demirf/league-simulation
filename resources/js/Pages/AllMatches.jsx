import Layout from "../Components/Layout.jsx";
import {Button} from "../Components/Button.jsx";
import {useForm} from "@inertiajs/inertia-react";
import MatchCard from "../Components/MatchCard.jsx";

const AllMatches = ({ allMatches }) => {
    const form = useForm();

    const handleStartSimulation = () => {
        const firstWeek = 1;
        form.get(`/tournaments/${allMatches[0][0].tournament_id}/matches/${firstWeek}`);
    }

    return (
        <Layout>
            <div className={"grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-3 xl:gap-x-8"}>
                {allMatches.map((matches, index) => <MatchCard matches={matches} currentWeek={index + 1} />)}
            </div>
            <div className={"flex justify-center mt-4"}>
                <Button onClick={handleStartSimulation}>Start Simulation</Button>
            </div>
        </Layout>
    )
}

export default AllMatches;
