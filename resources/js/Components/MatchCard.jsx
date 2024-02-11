import {MATCH_STATUS} from "../../constants/index.js";


const MatchCard = ({matches}) => {
    return (
        <div className={"flex flex-col items-start gap-4 border border-gray-100 rounded-lg relative max-w-[400px] w-full"}>
            <div className={"bg-gray-800 w-full p-4 rounded-lg rounded-bl-none rounded-br-none"}>
                <span className={"text-white text-lg font-medium"}>Week {matches[0].week}</span>
            </div>
            {matches.map((match, matchIndex) => <div key={matchIndex} className={"flex justify-start gap-4 p-4"}>
                <img src={match.home_team_logo} alt={"home team logo"} className={"w-6 h-6"}/>
                <span>{match.home_team}</span>
                {match.status === MATCH_STATUS.COMPLETE ? <span>{match.home_team_goals} - {match.away_team_goals}</span>: '-'}
                <img src={match.away_team_logo} alt="away team logo" className={"w-6 h-6"}/>
                <span>{match.away_team}</span>
            </div>)}
        </div>
    )
}

export default MatchCard;
