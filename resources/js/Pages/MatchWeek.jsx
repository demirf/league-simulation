import Layout from "../Components/Layout.jsx";
import MatchStandings from "../Components/MatchStandings.jsx";
import MatchCard from "../Components/MatchCard.jsx";
import {Button} from "../Components/Button.jsx";
import {useForm} from "@inertiajs/inertia-react";
import {MATCH_STATUS} from "../../constants/index.js";

const MatchWeek = (props) => {
    const form = useForm();
    const tournamentId = props.matches[0].tournament_id;
    const currentWeek = props.matches[0].week;
    const isCurrentWeekCompleted = props.matches.every(match => match.status === MATCH_STATUS.COMPLETE);
    const isTournamentCompleted = currentWeek === 6 && isCurrentWeekCompleted;


    const handlePlay = () => {
        if (isTournamentCompleted ){
            return form.get('/');
        }

        if (isCurrentWeekCompleted) {
            return form.get(`/tournaments/${tournamentId}/matches/${currentWeek + 1}`);
        }

        form.post(`/tournaments/${tournamentId}/matches/${currentWeek}/play`);
    }

    const handlePlayAll = () => {
        form.post(`/tournaments/${tournamentId}/matches/playAll`);
    }


    return (
        <Layout>
            <div className="flex md:mx-auto flex-col max-w-7xl justify-center">
                <div className="flex flex-col lg:flex-row gap-5 items-start">
                    <MatchStandings matchStandings={props.matchStandings} />
                    <MatchCard matches={props.matches} />
                </div>
                <div className={"mt-10 flex items-center gap-4  "}>
                    <Button onClick={handlePlayAll}>
                        Play All
                    </Button>
                    <Button onClick={handlePlay}>
                        {isTournamentCompleted ? 'Done' :isCurrentWeekCompleted ? 'Next Week' : 'Play'}
                    </Button>
                </div>
            </div>
        </Layout>
    )
}

export default MatchWeek;
