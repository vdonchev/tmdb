<?php

namespace App\Enum;

enum MovieSort: string
{
    case OriginalTitleAsc = 'original_title.asc';
    case OriginalTitleDesc = 'original_title.desc';
    case PopularityAsc = 'popularity.asc';
    case PopularityDesc = 'popularity.desc';
    case RevenueAsc = 'revenue.asc';
    case RevenueDesc = 'revenue.desc';
    case PrimaryReleaseDateAsc = 'primary_release_date.asc';
    case PrimaryReleaseDateDesc = 'primary_release_date.desc';
    case TitleAsc = 'title.asc';
    case TitleDesc = 'title.desc';
    case VoteAverageAsc = 'vote_average.asc';
    case VoteAverageDesc = 'vote_average.desc';
    case VoteCountAsc = 'vote_count.asc';
    case VoteCountDesc = 'vote_count.desc';
}
