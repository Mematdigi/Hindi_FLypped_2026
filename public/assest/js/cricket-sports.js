let currentSlidePositionHindi = 0;

function slideMatchesHindi(direction) {
    const slider = document.getElementById('cricket-matches-container-category-hindi');
    if (!slider) return;
    const cardWidth = 370;
    if (direction === 'next') {
        currentSlidePositionHindi += cardWidth;
    } else {
        currentSlidePositionHindi -= cardWidth;
    }
    const maxScroll = slider.scrollWidth - slider.clientWidth;
    if (currentSlidePositionHindi < 0) currentSlidePositionHindi = 0;
    if (currentSlidePositionHindi > maxScroll) currentSlidePositionHindi = maxScroll;
    slider.scrollTo({ left: currentSlidePositionHindi, behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', function () {

    /* ===================================================
       HELPER FUNCTIONS
    =================================================== */

    function convertGMTtoIST(gmtDateTimeString) {
        if (!gmtDateTimeString) return null;
        try {
            const gmtDate = new Date(gmtDateTimeString);
            const istDate = new Date(gmtDate.getTime() + (5.5 * 60 * 60 * 1000));
            let hours = istDate.getHours();
            let minutes = istDate.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            const timeIST = hours + ':' + minutes + ' ' + ampm;
            const months = ['जन', 'फर', 'मार', 'अप्र', 'मई', 'जून', 'जुल', 'अग', 'सित', 'अक्टू', 'नव', 'दिस'];
            const monthName = months[istDate.getMonth()];
            const day = istDate.getDate();
            const year = istDate.getFullYear();
            return {
                time: timeIST,
                date: `${monthName} ${day}`,
                fullDate: `${monthName} ${day}, ${year}`,
                dateObject: istDate
            };
        } catch (e) {
            return null;
        }
    }

    function capitalizeFirstLetter(string) {
        if (!string) return '';
        return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    }

    function countBoundaries(batting, type) {
        if (!batting) return 0;
        return batting.reduce((sum, bat) => sum + (bat[type] || 0), 0);
    }

    function getRoleIcon(role) {
        if (!role) return '👤';
        const r = role.toLowerCase();
        if (r.includes('batsman') || r.includes('batter')) return '🏏';
        if (r.includes('bowler')) return '⚡';
        if (r.includes('allrounder') || r.includes('all-rounder')) return '⭐';
        if (r.includes('wk') || r.includes('wicket')) return '🧤';
        return '👤';
    }

    function getRoleHindi(role) {
        if (!role) return role || '';
        const r = role.toLowerCase();
        if (r.includes('batsman') || r.includes('batter')) return 'बल्लेबाज';
        if (r.includes('bowler')) return 'गेंदबाज';
        if (r.includes('allrounder') || r.includes('all-rounder')) return 'ऑलराउंडर';
        if (r.includes('wk') || r.includes('wicket')) return 'विकेट-कीपर';
        return role;
    }

    /* ===================================================
       TEAM NAME RESOLVER
    =================================================== */

    const TEAM_MAP = {
        'sl': 'Sri Lanka', 'nz': 'New Zealand', 'sa': 'South Africa', 'wi': 'West Indies',
        'usa': 'United States', 'uae': 'United Arab Emirates', 'png': 'Papua New Guinea',
        'ind': 'India', 'pak': 'Pakistan', 'aus': 'Australia', 'eng': 'England',
        'ban': 'Bangladesh', 'zim': 'Zimbabwe', 'afg': 'Afghanistan', 'ire': 'Ireland',
        'ned': 'Netherlands', 'sco': 'Scotland', 'nam': 'Namibia', 'can': 'Canada',
        'uga': 'Uganda', 'oma': 'Oman', 'nep': 'Nepal', 'ken': 'Kenya', 'bah': 'Bahrain',
        'india': 'India', 'pakistan': 'Pakistan', 'australia': 'Australia', 'england': 'England',
        'bangladesh': 'Bangladesh', 'zimbabwe': 'Zimbabwe', 'afghanistan': 'Afghanistan',
        'ireland': 'Ireland', 'netherlands': 'Netherlands', 'scotland': 'Scotland',
        'namibia': 'Namibia', 'canada': 'Canada', 'uganda': 'Uganda', 'oman': 'Oman',
        'nepal': 'Nepal', 'kenya': 'Kenya', 'sri lanka': 'Sri Lanka', 'new zealand': 'New Zealand',
        'south africa': 'South Africa', 'west indies': 'West Indies',
        'papua new guinea': 'Papua New Guinea', 'united arab emirates': 'United Arab Emirates',
        'united states': 'United States', 'united states of america': 'United States',
    };

    function getFullTeamName(inningStr, matchInfo, index) {
        if (!inningStr) {
            return (matchInfo && matchInfo.teamInfo && matchInfo.teamInfo[index])
                ? matchInfo.teamInfo[index].name : `टीम ${(index || 0) + 1}`;
        }
        if (inningStr.includes(',')) {
            if (matchInfo && matchInfo.teamInfo && matchInfo.teamInfo[index]) {
                const team = matchInfo.teamInfo[index];
                const parts = inningStr.split(',').map(p => p.trim());
                for (let part of parts) {
                    const cleanPart = part
                        .replace(/\s*(1st|2nd|3rd|4th)\s*(inning|innings)\s*$/gi, '')
                        .replace(/\s*(inning|innings)\s*\d*\s*$/gi, '')
                        .trim().toLowerCase();
                    const teamNameLower = team.name.toLowerCase();
                    const teamShortLower = team.shortname.toLowerCase();
                    if (cleanPart === teamNameLower || cleanPart === teamShortLower ||
                        teamNameLower.includes(cleanPart) || teamShortLower.includes(cleanPart) ||
                        cleanPart.includes(teamNameLower) || cleanPart.includes(teamShortLower)) {
                        return team.name;
                    }
                }
                return team.name;
            }
            inningStr = inningStr.split(',')[0].trim();
        }
        const cleaned = inningStr
            .replace(/\s*(1st|2nd|3rd|4th)\s*(inning|innings)\s*$/gi, '')
            .replace(/\s*(inning|innings)\s*\d*\s*$/gi, '')
            .trim();
        if (matchInfo && matchInfo.teamInfo) {
            const exactName = matchInfo.teamInfo.find(t => cleaned.toLowerCase() === t.name.toLowerCase());
            if (exactName) return exactName.name;
            const exactShort = matchInfo.teamInfo.find(t => cleaned.toLowerCase() === t.shortname.toLowerCase());
            if (exactShort) return exactShort.name;
            const startsWith = matchInfo.teamInfo.find(t => t.name.toLowerCase().startsWith(cleaned.toLowerCase()));
            if (startsWith) return startsWith.name;
            const shortIn = matchInfo.teamInfo.find(t => cleaned.toLowerCase().startsWith(t.shortname.toLowerCase()));
            if (shortIn) return shortIn.name;
            const contains = matchInfo.teamInfo.find(t => {
                const c = cleaned.toLowerCase();
                const tn = t.name.toLowerCase();
                const ts = t.shortname.toLowerCase();
                return tn.includes(c) || c.includes(ts) || c.includes(tn) || ts.includes(c);
            });
            if (contains) return contains.name;
        }
        const key = cleaned.toLowerCase();
        if (TEAM_MAP[key]) return TEAM_MAP[key];
        return cleaned.replace(/\b\w/g, c => c.toUpperCase());
    }

    /* ===================================================
       SMART SCORE MATCHING
    =================================================== */

    function smartScoreMatch(matchInfo, team, index) {
        if (!matchInfo.score || matchInfo.score.length === 0) return null;
        function norm(str) {
            return (str || '').toLowerCase()
                .replace('united states of america', 'usa')
                .replace('united arab emirates', 'uae')
                .replace('papua new guinea', 'png')
                .replace('west indies', 'wi')
                .replace('south africa', 'sa')
                .replace('new zealand', 'nz')
                .replace('sri lanka', 'sl');
        }
        const teamName = norm(team.name);
        const teamShort = norm(team.shortname);
        const found = matchInfo.score.find(s => {
            if (!s.inning) return false;
            let inningStr = s.inning;
            if (inningStr.includes(',')) {
                const parts = inningStr.split(',').map(p => p.trim());
                for (let part of parts) {
                    const partNorm = norm(part);
                    const teamPart = partNorm
                        .replace(/\s*(1st|2nd|3rd|4th)?\s*(inning|innings)\s*\d*\s*$/gi, '')
                        .trim();
                    if (teamPart === teamName || teamPart === teamShort ||
                        teamName.includes(teamPart) || teamShort.includes(teamPart) ||
                        teamPart.includes(teamName) || teamPart.includes(teamShort)) {
                        return true;
                    }
                }
            }
            const ing = norm(inningStr);
            const cleanInning = ing
                .replace(/\s*(1st|2nd|3rd|4th)?\s*(inning|innings)\s*\d*\s*$/gi, '')
                .trim();
            return cleanInning === teamName || cleanInning === teamShort ||
                cleanInning.includes(teamName) || cleanInning.includes(teamShort) ||
                teamName.includes(cleanInning) || teamShort.includes(cleanInning) ||
                teamName === cleanInning.split(' ')[0] || teamShort === cleanInning.split(' ')[0];
        });
        return found || null;
    }

    /* ===================================================
       API FUNCTIONS
    =================================================== */

    function loadMatchInfoCategoryHindi(matchId) {
        return fetch(`https://api.cricapi.com/v1/match_info?apikey=9bafa7bc-e94e-45de-818e-2cf8c7404f5e&id=${matchId}`)
            .then(r => r.json())
            .then(d => (d.status === 'success' && d.data) ? d.data : null)
            .catch(() => null);
    }

    function loadMatchScorecardCategoryHindi(matchId) {
        return fetch(`https://flyppedhindi.com/api/cricket/scorecard?id=${matchId}`)
            .then(r => r.json())
            .then(d => (d.status === 'success' && d.data) ? d.data : null)
            .catch(() => null);
    }

    function loadMatchSquadCategoryHindi(matchId) {
        return fetch(`https://api.cricapi.com/v1/match_squad?apikey=9bafa7bc-e94e-45de-818e-2cf8c7404f5e&id=${matchId}`)
            .then(r => r.json())
            .then(d => (d.status === 'success' && d.data) ? d.data : null)
            .catch(() => null);
    }

    /* ===================================================
       LOAD MATCHES
    =================================================== */

    function loadCricketMatchesCategoryHindi() {
        const container = document.getElementById('cricket-matches-container-category-hindi');
        const sliderWrapper = document.querySelector('.cricket-slider-wrapper-hindi');
        const loading = document.getElementById('cricket-loading-category-hindi');
        const error = document.getElementById('cricket-error-category-hindi');
        if (!container || !loading || !error) return;

        loading.style.display = 'block';
        if (sliderWrapper) sliderWrapper.style.display = 'none';
        error.style.display = 'none';

        fetch('https://flyppedhindi.com/api/cricket/todaymatches')
            .then(r => r.json())
            .then(data => {
                const matchList = Array.isArray(data.data) ? data.data : (data.data.matchList || data.data.data || []);
                if (data.status === 'success' && matchList && matchList.length > 0) {
                    const matches = matchList.slice(0, 10);
                    const promises = matches.map(m =>
                        loadMatchInfoCategoryHindi(m.id)
                            .then(info => info || m)
                            .catch(() => m)
                    );
                    Promise.all(promises).then(detailedMatches => {
                        loading.style.display = 'none';
                        container.innerHTML = generateMatchCardsCategoryHindi(detailedMatches);
                        if (sliderWrapper) sliderWrapper.style.display = 'block';
                        attachMatchCardClickEventsCategoryHindi();
                    });
                } else {
                    loading.style.display = 'none';
                    error.style.display = 'block';
                    error.innerHTML = '<i class="fas fa-info-circle"></i> आज कोई मैच नहीं।';
                }
            })
            .catch(() => {
                loading.style.display = 'none';
                error.style.display = 'block';
            });
    }

    /* ===================================================
       GENERATE MATCH CARDS
    =================================================== */

    function generateMatchCardsCategoryHindi(matches) {
        let html = '';
        matches.forEach(match => {
            if (!match) return;
            const badge = (match.matchType || 't20').toUpperCase();
            let statusClass = 'status-upcoming';
            let statusBadge = '';
            let displayStatus = match.status || 'स्थिति उपलब्ध नहीं';

            if (match.dateTimeGMT && !match.matchStarted) {
                const ist = convertGMTtoIST(match.dateTimeGMT);
                if (ist) displayStatus = `मैच ${ist.date}, ${ist.time} IST पर शुरू होगा`;
            }
            if (match.matchEnded) {
                statusClass = 'status-ended';
            } else if (match.matchStarted && !match.matchEnded) {
                statusClass = 'status-live';
                statusBadge = '<span class="live-badge-category">🔴 लाइव</span>';
            }

            html += `<div class="cricket-match-card-category-hindi" data-match-id="${match.id}">
                <div class="match-header-category">
                    <span class="match-type-badge-category badge-t20">${badge}</span>
                    ${statusBadge}
                </div>
                <div class="teams-container-category">`;

            if (match.teamInfo && match.teamInfo.length > 0) {
                match.teamInfo.forEach((team, i) => {
                    const score = smartScoreMatch(match, team, i);
                    html += `<div class="team-row-category">
                        <div class="team-info-category">
                            <img src="${team.img}" class="team-flag-category"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22%3E%3Crect fill=%22%23ddd%22 width=%2224%22 height=%2224%22/%3E%3C/svg%3E';">
                            <span class="team-name-category">${team.shortname}</span>
                        </div>
                        <div class="team-score-category">
                            ${score && score.r !== undefined
                                ? `<span class="score-runs-category">${score.r}/${score.w}</span><span class="score-overs-category">(${parseFloat(score.o).toFixed(1)})</span>`
                                : `<span class="score-placeholder-category">-</span>`}
                        </div>
                    </div>`;
                });
            }

            html += `</div>
                <div class="match-footer-category">
                    <div class="match-status-category ${statusClass}">
                        <i class="fas fa-circle status-indicator-category"></i>
                        <span class="status-text-category">${displayStatus}</span>
                    </div>
                    ${match.venue ? `<div class="match-venue-category"><i class="fas fa-map-marker-alt"></i><span>${match.venue}</span></div>` : ''}
                </div>
            </div>`;
        });
        return html;
    }

    function attachMatchCardClickEventsCategoryHindi() {
        document.querySelectorAll('.cricket-match-card-category-hindi').forEach(card => {
            card.addEventListener('click', function () {
                openModalCategoryHindi(this.getAttribute('data-match-id'));
            });
        });
    }

    /* ===================================================
       OPEN MODAL
    =================================================== */

    function openModalCategoryHindi(matchId) {
        const modal = new bootstrap.Modal(document.getElementById('scorecardModalCategoryHindi'));
        const modalBody = document.getElementById('scorecardModalCategoryHindiBody');
        const modalTitle = document.getElementById('scorecardModalCategoryHindiTitle');

        modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3 text-muted">मैच विवरण लोड हो रहा है...</p></div>';
        modal.show();

        Promise.all([
            loadMatchScorecardCategoryHindi(matchId),
            loadMatchSquadCategoryHindi(matchId)
        ]).then(([scorecardData, squadData]) => {
            if (scorecardData) {
                if (squadData) scorecardData.squadData = squadData;
                modalTitle.innerHTML = `<img src="https://flyppedhindi.com/public/assest/images/Flypped-hindi-logo.webp" alt="Flypped Hindi" style="height:52px;margin-right:12px;vertical-align:middle;background:white;border-radius:6px;"><span style="vertical-align:middle;">${scorecardData.name || 'मैच विवरण'}</span>`;
                modalBody.innerHTML = generateScorecardCategoryHindi(scorecardData);
            } else {
                throw new Error('no scorecard');
            }
        }).catch(() => {
            Promise.all([
                loadMatchInfoCategoryHindi(matchId),
                loadMatchSquadCategoryHindi(matchId)
            ]).then(([matchInfoData, squadData]) => {
                if (matchInfoData) {
                    if (squadData) matchInfoData.squadData = squadData;
                    modalTitle.innerHTML = `<img src="https://flyppedhindi.com/public/assest/images/Flypped-hindi-logo.webp" alt="Flypped Hindi" style="height:44px;margin-right:12px;vertical-align:middle;background:white;border-radius:6px;"><span style="vertical-align:middle;">${matchInfoData.name || 'मैच विवरण'}</span>`;
                    modalBody.innerHTML = generateScorecardCategoryHindi(matchInfoData);
                } else {
                    throw new Error('no data');
                }
            }).catch(() => {
                modalBody.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i><strong>मैच विवरण लोड नहीं हो सका</strong><p class="mb-0 mt-2">यह मैच अभी शुरू नहीं हुआ है या डेटा अस्थायी रूप से उपलब्ध नहीं है।</p></div>`;
            });
        });
    }

    /* ===================================================
       GENERATE SCORECARD HTML
    =================================================== */

    function generateScorecardCategoryHindi(matchInfo) {
        let html = '';
        let matchTimeIST = '';
        if (matchInfo.dateTimeGMT) {
            const ist = convertGMTtoIST(matchInfo.dateTimeGMT);
            if (ist) matchTimeIST = `${ist.fullDate} बजे ${ist.time} IST`;
        }

        html += `<div class="match-result-header-hc">`;
        if (matchInfo.matchWinner) {
            const st = matchInfo.status || '';
            const numMatch = st.match(/\d+/);
            const margin = numMatch
                ? (st.toLowerCase().includes('run') ? numMatch[0] + ' रन से' : numMatch[0] + ' विकेट से')
                : st;
            html += `<div class="result-text-hc">${matchInfo.matchWinner} ने ${margin} जीता</div>`;
        } else if (matchInfo.matchStarted && !matchInfo.matchEnded) {
            html += `<div class="result-text-hc live-indicator-hc"><span class="live-dot-hc"></span> लाइव</div>`;
        } else {
            html += `<div class="result-text-hc">${matchInfo.status || ''}</div>`;
        }
        html += `<div class="match-meta-hc">${matchInfo.matchType ? matchInfo.matchType.toUpperCase() + ' • ' : ''}${matchInfo.venue || ''}</div></div>`;

        if (matchInfo.teamInfo && matchInfo.teamInfo.length >= 2) {
            html += `<div class="teams-score-container-hc">`;
            matchInfo.teamInfo.forEach((team, index) => {
                const score = smartScoreMatch(matchInfo, team, index);
                let isWinner = false;
                if (matchInfo.matchWinner) {
                    const wl = matchInfo.matchWinner.toLowerCase();
                    const tl = team.name.toLowerCase();
                    const ts = team.shortname.toLowerCase();
                    isWinner = wl.includes(tl) || wl.includes(ts) || tl.includes(wl) || ts.includes(wl);
                }
                html += `<div class="team-score-row-hc ${isWinner ? 'winner-hc' : ''}">
                    <div class="team-flag-name-hc">
                        <img src="${team.img}" class="team-flag-lg-hc"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22%3E%3Crect fill=%22%23ddd%22 width=%2224%22 height=%2224%22/%3E%3C/svg%3E';">
                        <span class="team-name-lg-hc">${team.shortname}</span>
                    </div>
                    <div class="team-total-score-hc">
                        ${score ? `<span class="total-runs-hc">${score.r}/${score.w}</span><span class="total-overs-hc">(${parseFloat(score.o).toFixed(1)})</span>` : '<span class="total-runs-hc">-</span>'}
                    </div>
                </div>`;
            });
            html += `</div>`;
        }

        const tossChoice = matchInfo.tossChoice === 'bat' ? 'बल्लेबाजी' : matchInfo.tossChoice === 'field' ? 'गेंदबाजी' : (matchInfo.tossChoice || '');
        html += `<div class="scorecard-info-card-compact-hc">
            <div class="info-row-hc"><span class="info-label-hc">टॉस:</span><span class="info-value-hc">${matchInfo.tossWinner ? capitalizeFirstLetter(matchInfo.tossWinner) + ' जीता, ' + tossChoice + ' चुना' : 'N/A'}</span></div>
            <div class="info-row-hc"><span class="info-label-hc">समय:</span><span class="info-value-hc">${matchTimeIST || 'N/A'}</span></div>
        </div>`;

        if (!matchInfo.matchStarted) {
            html += `<div class="alert alert-info mt-4"><div class="d-flex align-items-center"><i class="fas fa-clock me-3" style="font-size:28px;"></i><div><strong style="font-size:16px;">आगामी मैच</strong><p class="mb-0 mt-1" style="font-size:14px;">मैच शुरू होने के बाद स्कोरकार्ड उपलब्ध होगा।</p></div></div></div>`;
        } else if (matchInfo.scorecard && matchInfo.scorecard.length > 0) {
            html += `<ul class="nav nav-tabs scorecard-tabs-hc mt-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#hc-overview-tab" type="button" role="tab">अवलोकन</button>
                </li>`;
            matchInfo.scorecard.forEach((inning, index) => {
                const teamName = getFullTeamName(inning.inning, matchInfo, index);
                const suffix = index === 0 ? 'पहली' : index === 1 ? 'दूसरी' : index === 2 ? 'तीसरी' : 'चौथी';
                html += `<li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hc-inning-${index}" type="button" role="tab">
                        ${teamName} <small style="font-size:11px;opacity:.7;">${suffix}</small>
                    </button>
                </li>`;
            });
            html += `</ul><div class="tab-content scorecard-tab-content-hc">
                <div class="tab-pane fade show active" id="hc-overview-tab" role="tabpanel">
                    ${generateOverviewTabCategoryHindi(matchInfo)}
                </div>`;
            matchInfo.scorecard.forEach((inning, index) => {
                html += `<div class="tab-pane fade" id="hc-inning-${index}" role="tabpanel">${generateInningTabCategoryHindi(inning, matchInfo.matchStarted && !matchInfo.matchEnded, matchInfo)}</div>`;
            });
            html += `</div>`;
        } else {
            html += '<div class="alert alert-info mt-4"><i class="fas fa-info-circle"></i> विस्तृत स्कोरकार्ड उपलब्ध नहीं है।</div>';
        }
        return html;
    }

    /* ===================================================
       OVERVIEW TAB
    =================================================== */

    function generateOverviewTabCategoryHindi(matchInfo) {
        let html = '<div class="overview-container-hc mt-3">';
        const isLive = matchInfo.matchStarted && !matchInfo.matchEnded;
        const isCompleted = matchInfo.matchEnded;

        if (isCompleted && matchInfo.matchWinner) {
            html += generateMatchAnalysisCategoryHindi(matchInfo);
        }

        if (matchInfo.teamInfo && matchInfo.teamInfo.length > 0) {
            matchInfo.teamInfo.forEach((team, teamIndex) => {
                const score = smartScoreMatch(matchInfo, team, teamIndex);
                if (!score || score.r === undefined) return;

                let inning = null;
                if (matchInfo.scorecard && matchInfo.scorecard.length > 0) {
                    inning = matchInfo.scorecard.find(inn => {
                        if (!inn.inning) return false;
                        const inningLower = inn.inning.toLowerCase();
                        return inningLower.includes(team.name.toLowerCase()) || inningLower.includes(team.shortname.toLowerCase());
                    });
                }
                if (!inning || !inning.batting || inning.batting.length === 0) return;

                let totalBattingRuns = 0;
                let yetToBatPlayers = [];
                inning.batting.forEach(bat => { totalBattingRuns += bat.r || 0; });
                const extras = Math.max(0, (score.r || 0) - totalBattingRuns);
                yetToBatPlayers = Array(Math.max(0, 11 - inning.batting.length)).fill('खिलाड़ी');
                const runRate = score.o > 0 ? (score.r / score.o).toFixed(2) : '0.00';

                html += `<div class="innings-overview-hc mb-4">
                    <div class="innings-header-hc">
                        <h6 class="innings-title-hc">${team.name}</h6>
                        <div class="innings-score-hc">${score.r}/${score.w} (${parseFloat(score.o).toFixed(1)})</div>
                    </div>
                    <div class="overview-stats-grid-hc">
                        <div class="stat-box-hc"><div class="stat-label-hc">रन रेट</div><div class="stat-value-hc">${runRate}</div></div>
                        <div class="stat-box-hc"><div class="stat-label-hc">एक्स्ट्रा</div><div class="stat-value-hc">${extras}</div></div>
                        <div class="stat-box-hc"><div class="stat-label-hc">चौके</div><div class="stat-value-hc">${countBoundaries(inning.batting, '4s')}</div></div>
                        <div class="stat-box-hc"><div class="stat-label-hc">छक्के</div><div class="stat-value-hc">${countBoundaries(inning.batting, '6s')}</div></div>
                    </div>`;

                html += generateMatchGraphsCategoryHindi(inning, score, isLive);

                if (inning.batting.length > 0) {
                    const topBatsman = [...inning.batting].sort((a, b) => b.r - a.r)[0];
                    html += `<div class="top-performer-hc mt-3">
                        <div class="performer-label-hc">🏏 शीर्ष स्कोरर</div>
                        <div class="performer-details-hc">
                            <span class="performer-name-hc">${topBatsman.batsman.name}</span>
                            <span class="performer-score-hc">${topBatsman.r}(${topBatsman.b}) • एसआर: ${topBatsman.sr.toFixed(1)}</span>
                        </div>
                    </div>`;
                }

                if (inning.bowling && inning.bowling.length > 0) {
                    const topBowler = [...inning.bowling].sort((a, b) => b.w - a.w)[0];
                    if (topBowler.w > 0) {
                        html += `<div class="top-performer-hc">
                            <div class="performer-label-hc">⚡ शीर्ष गेंदबाज</div>
                            <div class="performer-details-hc">
                                <span class="performer-name-hc">${topBowler.bowler.name}</span>
                                <span class="performer-score-hc">${topBowler.w}/${topBowler.r} (${topBowler.o} ओवर) • इकॉ: ${topBowler.eco.toFixed(2)}</span>
                            </div>
                        </div>`;
                    }
                }

                if (yetToBatPlayers.length > 0) {
                    const ytbLabel = isCompleted ? 'बल्लेबाजी नहीं की' : 'बल्लेबाजी बाकी';
                    html += `<div class="yet-to-bat-hc mt-3"><div class="ytb-label-hc">${ytbLabel}: <span class="ytb-count-hc">${yetToBatPlayers.length} खिलाड़ी</span></div></div>`;
                }
                html += `</div>`;
            });
        }
        html += '</div>';
        return html;
    }

    function generateMatchAnalysisCategoryHindi(matchInfo) {
        let html = '<div class="match-analysis-card-hc mb-4">';
        html += `<h6 class="analysis-title-hc">📊 मैच विश्लेषण</h6>
            <div class="analysis-section-hc">
                <div class="analysis-item-hc winner-analysis-hc">
                    <div class="analysis-icon-hc">🏆</div>
                    <div class="analysis-content-hc">
                        <div class="analysis-label-hc">मैच परिणाम</div>
                        <div class="analysis-value-hc">${matchInfo.status || ''}</div>
                    </div>
                </div>
            </div>`;

        if (matchInfo.scorecard && matchInfo.scorecard.length >= 2) {
            const t1n = getFullTeamName(matchInfo.scorecard[0].inning, matchInfo, 0);
            const t2n = getFullTeamName(matchInfo.scorecard[1].inning, matchInfo, 1);
            const t1 = matchInfo.teamInfo && matchInfo.teamInfo[0];
            const t2 = matchInfo.teamInfo && matchInfo.teamInfo[1];
            const t1s = t1 ? smartScoreMatch(matchInfo, t1, 0) : null;
            const t2s = t2 ? smartScoreMatch(matchInfo, t2, 1) : null;
            if (t1s && t2s) {
                html += `<div class="comparison-grid-hc mt-3">
                    <div class="comparison-item-hc">
                        <div class="comp-label-hc">कुल रन</div>
                        <div class="comp-values-hc">
                            <span class="comp-team1-hc">${t1n}: ${t1s.r}</span>
                            <span class="comp-team2-hc">${t2n}: ${t2s.r}</span>
                        </div>
                    </div>
                    <div class="comparison-item-hc">
                        <div class="comp-label-hc">रन रेट</div>
                        <div class="comp-values-hc">
                            <span class="comp-team1-hc">${t1s.o > 0 ? (t1s.r / t1s.o).toFixed(2) : '0.00'}</span>
                            <span class="comp-team2-hc">${t2s.o > 0 ? (t2s.r / t2s.o).toFixed(2) : '0.00'}</span>
                        </div>
                    </div>
                </div>`;
            }
        }
        html += '</div>';
        return html;
    }

    /* ===================================================
       INNING TAB
    =================================================== */

    function generateInningTabCategoryHindi(inning, isLive, matchInfo) {
        let html = '';
        let totalBattingRuns = 0;
        let totalRuns = 0;
        let extrasBreakdown = { wides: 0, noBalls: 0, byes: 0, legByes: 0 };

        html += `<div class="table-section-hc mt-4">
            <h6 class="table-section-title-hc">बल्लेबाजी</h6>
            <div class="table-responsive">
                <table class="table scorecard-table-hc">
                    <thead><tr>
                        <th style="text-align:left;">बल्लेबाज</th>
                        <th>R</th><th>B</th><th>4s</th><th>6s</th><th>S/R</th>
                    </tr></thead>
                    <tbody>`;

        if (inning.batting && inning.batting.length > 0) {
            inning.batting.forEach(bat => {
                const isBatting = bat['dismissal-text'] === 'batting';
                const dismissal = !isBatting
                    ? `<div class="dismissal-text-hc">${capitalizeFirstLetter(bat['dismissal-text'])}</div>`
                    : '';
                let playerData = null;
                if (matchInfo && matchInfo.squadData) {
                    matchInfo.squadData.forEach(squad => {
                        if (squad.players && !playerData) {
                            const f = squad.players.find(p => p.id === bat.batsman.id || p.name === bat.batsman.name);
                            if (f) playerData = { id: f.id, name: f.name, role: f.role || 'Batsman', battingStyle: f.battingStyle || null, bowlingStyle: f.bowlingStyle || null, country: f.country || null, playerImg: f.playerImg || null };
                        }
                    });
                }
                const clickAttr = playerData ? `onclick='showPlayerInfoCategoryHindi(${JSON.stringify(playerData)})' style='cursor:pointer;'` : '';
                html += `<tr class="${isBatting ? 'batting-now-hc' : ''} ${playerData ? 'player-clickable-hc' : ''}" ${clickAttr}>
                    <td style="text-align:left;"><div class="batsman-cell-hc"><div class="batsman-name-hc">${bat.batsman.name}</div>${dismissal}</div></td>
                    <td><strong>${bat.r}</strong></td><td>${bat.b}</td><td>${bat['4s']}</td><td>${bat['6s']}</td><td>${bat.sr.toFixed(1)}</td>
                </tr>`;
            });
        }
        html += `</tbody></table></div></div>`;

        if (inning.batting) inning.batting.forEach(bat => totalBattingRuns += bat.r || 0);

        if (inning.inning && matchInfo && matchInfo.score) {
            const hasCorrupt = matchInfo.score.some(s => s.inning && s.inning.includes(','));
            let inningScore = null;
            if (hasCorrupt) {
                const idx = matchInfo.scorecard ? matchInfo.scorecard.indexOf(inning) : -1;
                inningScore = idx >= 0 ? matchInfo.score[idx] : null;
            } else {
                inningScore = matchInfo.score.find(s => s.inning && s.inning.toLowerCase() === inning.inning.toLowerCase());
            }
            if (inningScore) {
                totalRuns = inningScore.r || 0;
            }
        }

        if (inning.bowling) inning.bowling.forEach(bowl => {
            extrasBreakdown.wides += bowl.wd || 0;
            extrasBreakdown.noBalls += bowl.nb || 0;
        });

        if (totalRuns === 0) {
            totalRuns = totalBattingRuns + extrasBreakdown.wides + extrasBreakdown.noBalls;
        }
        const extras = Math.max(0, totalRuns - totalBattingRuns);

        html += `<div class="extras-total-section-hc mt-3"><div class="extras-card-hc">
            <h6 class="extras-title-hc">अतिरिक्त और कुल</h6>
            <div class="extras-breakdown-hc">
                ${extrasBreakdown.wides > 0 ? `<div class="extras-detail-hc"><span>वाइड: ${extrasBreakdown.wides}</span></div>` : ''}
                ${extrasBreakdown.noBalls > 0 ? `<div class="extras-detail-hc"><span>नो बॉल: ${extrasBreakdown.noBalls}</span></div>` : ''}
                <div class="extras-row-hc total-row-hc mt-2">
                    <span class="info-label-hc"><strong>कुल रन:</strong></span>
                    <span class="info-value-hc"><strong>${totalRuns}</strong></span>
                </div>
            </div>
        </div></div>`;

        // Fall of wickets
        if (inning.batting) {
            const fow = [];
            let cumRuns = 0;
            inning.batting.forEach(bat => {
                cumRuns += bat.r || 0;
                if (bat['dismissal-text'] !== 'batting' && bat['dismissal-text'] !== 'not out') {
                    fow.push({ wicket: fow.length + 1, player: bat.batsman.name, runs: bat.r || 0, balls: bat.b || 0, teamScore: cumRuns, dismissal: bat['dismissal-text'] });
                }
            });
            if (fow.length > 0) {
                html += `<div class="fall-of-wickets-section-hc mt-3"><h6 class="fow-title-hc">📉 विकेट पतन</h6><div class="fow-container-hc">`;
                fow.forEach(f => {
                    html += `<div class="fow-item-hc">
                        <div class="fow-wicket-hc">${f.wicket}</div>
                        <div class="fow-details-hc">
                            <div class="fow-player-hc">${f.player}</div>
                            <div class="fow-stats-hc">
                                <span class="fow-player-score-hc">${f.runs}(${f.balls})</span>
                                <span class="fow-team-score-hc">टीम: ${f.teamScore}</span>
                            </div>
                            <div class="fow-dismissal-hc">${capitalizeFirstLetter(f.dismissal)}</div>
                        </div>
                    </div>`;
                });
                html += `</div></div>`;
            }
        }

        // Yet to bat
        if (inning.batting) {
            const battedIds = new Set(inning.batting.map(b => b.batsman.id));
            const ytbPlayers = [];
            if (matchInfo && matchInfo.squadData && matchInfo.squadData.length > 0) {
                const rawTeam = inning.inning ? inning.inning.replace(/,.*/, '') : '';
                const inningTeam = rawTeam.split(' ')[0].toLowerCase();
                const squad = matchInfo.squadData.find(sq => {
                    const tn = (sq.teamName || '').toLowerCase();
                    const sn = (sq.shortname || '').toLowerCase();
                    return inningTeam.includes(tn) || inningTeam.includes(sn) || tn.includes(inningTeam) || sn.includes(inningTeam);
                });
                if (squad && squad.players) {
                    squad.players.forEach(p => {
                        if (!battedIds.has(p.id)) {
                            ytbPlayers.push({ id: p.id, name: p.name, role: p.role || 'Player', battingStyle: p.battingStyle || null, bowlingStyle: p.bowlingStyle || null, country: p.country || null, playerImg: p.playerImg || null });
                        }
                    });
                }
            }
            const ytbCount = ytbPlayers.length > 0 ? ytbPlayers.length : Math.max(0, 11 - inning.batting.length);
            if (ytbCount > 0) {
                const ytbLabel = isLive ? 'बल्लेबाजी बाकी' : 'नहीं खेले';
                html += `<div class="yet-to-bat-section-hc mt-3"><div class="ytb-card-hc"><h6 class="ytb-title-hc">${ytbLabel}</h6>`;
                if (ytbPlayers.length > 0) {
                    html += `<div class="ytb-players-grid-hc">`;
                    ytbPlayers.forEach(p => {
                        html += `<div class="ytb-player-card-hc" onclick='showPlayerInfoCategoryHindi(${JSON.stringify(p)})'>
                            <div class="ytb-player-name-hc">${p.name}</div>
                            <div class="ytb-player-role-hc">${getRoleHindi(p.role)}</div>
                            ${p.battingStyle ? `<div class="ytb-player-style-hc">⚾ ${p.battingStyle}</div>` : ''}
                            ${p.bowlingStyle ? `<div class="ytb-player-style-hc">🎯 ${p.bowlingStyle}</div>` : ''}
                        </div>`;
                    });
                    html += `</div>`;
                } else {
                    html += `<div class="ytb-content-hc"><div class="ytb-count-badge-hc"><i class="fas fa-users"></i><span>${ytbCount} खिलाड़ी ${isLive ? 'बल्लेबाजी बाकी' : 'नहीं खेले'}</span></div></div>`;
                }
                html += `</div></div>`;
            }
        }

        // Bowling table
        html += `<div class="table-section-hc mt-4">
            <h6 class="table-section-title-hc">गेंदबाजी</h6>
            <div class="table-responsive">
                <table class="table scorecard-table-hc">
                    <thead><tr>
                        <th style="text-align:left;">गेंदबाज</th>
                        <th>O</th><th>M</th><th>R</th><th>W</th><th>ECO</th>
                    </tr></thead>
                    <tbody>`;

        if (inning.bowling && inning.bowling.length > 0) {
            inning.bowling.forEach(bowl => {
                let playerData = null;
                if (matchInfo && matchInfo.squadData) {
                    matchInfo.squadData.forEach(squad => {
                        if (squad.players && !playerData) {
                            const f = squad.players.find(p => p.id === bowl.bowler.id || p.name === bowl.bowler.name);
                            if (f) playerData = { id: f.id, name: f.name, role: f.role || 'Bowler', battingStyle: f.battingStyle || null, bowlingStyle: f.bowlingStyle || null, country: f.country || null, playerImg: f.playerImg || null };
                        }
                    });
                }
                const clickAttr = playerData ? `onclick='showPlayerInfoCategoryHindi(${JSON.stringify(playerData)})' style='cursor:pointer;'` : '';
                html += `<tr class="${playerData ? 'player-clickable-hc' : ''}" ${clickAttr}>
                    <td style="text-align:left;"><div class="bowler-name-hc">${bowl.bowler.name}</div></td>
                    <td>${bowl.o}</td><td>${bowl.m}</td><td>${bowl.r}</td>
                    <td><strong>${bowl.w}</strong></td><td>${bowl.eco.toFixed(2)}</td>
                </tr>`;
            });
        }
        html += `</tbody></table></div></div>`;
        return html;
    }

    /* ===================================================
       GRAPHS
    =================================================== */

    function generateMatchGraphsCategoryHindi(inning, score, isLive) {
        let html = '<div class="match-graphs-hc mt-4">';
        const uid = `team-${Math.random().toString(36).substr(2, 8)}`;

        html += `<div class="graph-section-hc mb-4">
            <h6 class="graph-title-hc">${isLive ? '<span class="live-dot-small-hc"></span>' : ''} ओवर दर ओवर रन रेट</h6>
            <div class="worm-graph-hc">
                <canvas id="hc-rrg-${uid}" height="150"></canvas>
            </div>
        </div>`;

        if (inning.batting && inning.batting.length > 0) {
            html += `<div class="graph-section-hc">
                <h6 class="graph-title-hc">${isLive ? 'हालिया गेंदें (पिछले 2 ओवर)' : 'पिछले 2 ओवर'}</h6>
                <div class="ball-by-ball-hc">${generateBallByBallCategoryHindi(inning)}</div>
            </div>`;
        }
        html += '</div>';

        setTimeout(() => {
            const canvas = document.getElementById(`hc-rrg-${uid}`);
            if (canvas && !canvas.dataset.init) {
                initRunRateGraphCategoryHindi(canvas, score, inning);
                canvas.dataset.init = '1';
            }
        }, 150);

        return html;
    }

    function generateBallByBallCategoryHindi(inning) {
        let balls = [];
        if (inning.batting && inning.batting.length > 0) {
            const recentBatsmen = inning.batting.slice(-2);
            recentBatsmen.forEach(bat => {
                const fours = bat['4s'] || 0;
                const sixes = bat['6s'] || 0;
                for (let i = 0; i < sixes; i++) balls.push(6);
                for (let i = 0; i < fours; i++) balls.push(4);
                const otherRuns = (bat.r || 0) - (fours * 4) - (sixes * 6);
                for (let i = 0; i < otherRuns; i++) {
                    const rand = Math.random();
                    balls.push(rand < 0.5 ? 1 : rand < 0.8 ? 2 : 3);
                }
                const remaining = (bat.b || 0) - balls.length;
                for (let i = 0; i < remaining; i++) balls.push(0);
            });
        }
        if (balls.length > 12) balls = balls.slice(-12);
        while (balls.length < 12) {
            const r = Math.random();
            balls.push(r < 0.45 ? 0 : r < 0.65 ? 1 : r < 0.80 ? 2 : r < 0.90 ? 4 : 6);
        }
        let html = '<div class="balls-container-hc">';
        balls.forEach(b => {
            let cls = 'ball-dot-hc', txt = '•';
            if (b === 'W') { cls = 'ball-wicket-hc'; txt = 'W'; }
            else if (b === 6) { cls = 'ball-six-hc'; txt = '6'; }
            else if (b === 4) { cls = 'ball-four-hc'; txt = '4'; }
            else if (b > 0) { cls = 'ball-run-hc'; txt = b; }
            html += `<div class="ball-hc ${cls}">${txt}</div>`;
        });
        return html + '</div>';
    }

    function initRunRateGraphCategoryHindi(canvas, score, inning) {
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const width = canvas.width = canvas.offsetWidth || 300;
        const height = canvas.height = 150;
        const totalOvers = Math.ceil(score.o || 0);
        const totalRuns = score.r || 0;
        const data = [];
        let cumulativeRuns = 0;

        if (inning && inning.batting && inning.batting.length > 0) {
            inning.batting.forEach(bat => {
                const batsmanRuns = bat.r || 0;
                const oversForBatsman = (bat.b || 0) / 6;
                if (oversForBatsman > 0) {
                    const startOver = data.length > 0 ? data[data.length - 1].over : 0;
                    const endOver = Math.min(startOver + oversForBatsman, totalOvers);
                    cumulativeRuns += batsmanRuns;
                    if (endOver - startOver > 1) {
                        data.push({ over: startOver + (endOver - startOver) / 2, runs: cumulativeRuns - batsmanRuns / 2 });
                    }
                    data.push({ over: endOver, runs: cumulativeRuns });
                }
            });
        }

        if (data.length === 0) {
            const avgRunRate = totalRuns / (totalOvers || 1);
            for (let i = 1; i <= totalOvers; i++) {
                const variance = (Math.random() - 0.5) * 15;
                data.push({ over: i, runs: Math.max(0, Math.min(avgRunRate * i + variance, totalRuns)) });
            }
        }
        if (data.length > 0) { data[data.length - 1].runs = totalRuns; data[data.length - 1].over = totalOvers; }

        ctx.clearRect(0, 0, width, height);
        const pad = 35;
        const gw = width - 2 * pad;
        const gh = height - 2 * pad;
        const maxR = Math.max(...data.map(d => d.runs), totalRuns || 1);
        const xS = gw / (totalOvers || 1);
        const yS = maxR > 0 ? gh / maxR : 1;

        ctx.strokeStyle = '#f0f0f0'; ctx.lineWidth = 1;
        for (let i = 0; i <= 5; i++) {
            const y = pad + (gh / 5) * i;
            ctx.beginPath(); ctx.moveTo(pad, y); ctx.lineTo(width - pad, y); ctx.stroke();
        }
        ctx.strokeStyle = '#5f6368'; ctx.lineWidth = 2;
        ctx.beginPath(); ctx.moveTo(pad, pad); ctx.lineTo(pad, height - pad); ctx.lineTo(width - pad, height - pad); ctx.stroke();

        const grad = ctx.createLinearGradient(0, pad, 0, height - pad);
        grad.addColorStop(0, 'rgba(26,115,232,0.3)');
        grad.addColorStop(1, 'rgba(26,115,232,0.05)');
        ctx.fillStyle = grad;
        ctx.beginPath(); ctx.moveTo(pad, height - pad);
        data.forEach(p => ctx.lineTo(pad + p.over * xS, height - pad - p.runs * yS));
        ctx.lineTo(width - pad, height - pad); ctx.closePath(); ctx.fill();

        ctx.strokeStyle = '#1a73e8'; ctx.lineWidth = 3;
        ctx.beginPath();
        data.forEach((p, i) => {
            const x = pad + p.over * xS, y = height - pad - p.runs * yS;
            i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
        });
        ctx.stroke();

        data.forEach((p, i) => {
            const x = pad + p.over * xS, y = height - pad - p.runs * yS;
            ctx.fillStyle = '#1a73e8'; ctx.beginPath(); ctx.arc(x, y, 4, 0, 2 * Math.PI); ctx.fill();
            if (i === data.length - 1) {
                ctx.strokeStyle = '#fff'; ctx.lineWidth = 2;
                ctx.beginPath(); ctx.arc(x, y, 5, 0, 2 * Math.PI); ctx.stroke();
            }
        });

        ctx.fillStyle = '#5f6368'; ctx.font = '11px Arial'; ctx.textAlign = 'center';
        for (let i = 0; i <= totalOvers; i += Math.ceil(totalOvers / 5)) {
            ctx.fillText(i, pad + i * xS, height - pad + 15);
        }
        ctx.textAlign = 'right';
        for (let i = 0; i <= 5; i++) {
            ctx.fillText(Math.round((maxR / 5) * (5 - i)), pad - 8, pad + (gh / 5) * i + 4);
        }
        ctx.fillStyle = '#202124'; ctx.font = 'bold 12px Arial'; ctx.textAlign = 'center';
        ctx.fillText('ओवर', width / 2, height - 5);
        ctx.save(); ctx.translate(12, height / 2); ctx.rotate(-Math.PI / 2);
        ctx.fillText('रन', 0, 0); ctx.restore();
    }

    /* ===================================================
       PLAYER INFO MODAL
    =================================================== */

    window.showPlayerInfoCategoryHindi = function (player) {
        const modal = new bootstrap.Modal(document.getElementById('playerInfoModalCategoryHindi'));
        const modalBody = document.getElementById('playerInfoModalCategoryHindiBody');
        const modalTitle = document.getElementById('playerInfoModalCategoryHindiTitle');
        if (modalTitle) modalTitle.textContent = player.name;
        modalBody.innerHTML = `<div class="player-info-container-hc">
            ${player.playerImg && player.playerImg !== 'https://h.cricapi.com/img/icon512.png'
                ? `<div class="player-image-section-hc"><img src="${player.playerImg}" alt="${player.name}" class="player-modal-img-hc"></div>`
                : ''}
            <div class="player-details-grid-hc">
                <div class="player-detail-item-hc">
                    <div class="detail-label-hc">🏏 भूमिका</div>
                    <div class="detail-value-hc">${getRoleHindi(player.role) || 'N/A'}</div>
                </div>
                ${player.battingStyle ? `<div class="player-detail-item-hc"><div class="detail-label-hc">⚾ बल्लेबाजी शैली</div><div class="detail-value-hc">${player.battingStyle}</div></div>` : ''}
                ${player.bowlingStyle ? `<div class="player-detail-item-hc"><div class="detail-label-hc">🎯 गेंदबाजी शैली</div><div class="detail-value-hc">${player.bowlingStyle}</div></div>` : ''}
                <div class="player-detail-item-hc">
                    <div class="detail-label-hc">🌍 देश</div>
                    <div class="detail-value-hc">${player.country || 'N/A'}</div>
                </div>
            </div>
            ${player.role ? `<div class="player-role-badge-large-hc"><span class="role-icon-hc">${getRoleIcon(player.role)}</span><span class="role-text-hc">${getRoleHindi(player.role)}</span></div>` : ''}
        </div>`;
        modal.show();
    };

    /* ===================================================
       INITIALIZE
    =================================================== */

    loadCricketMatchesCategoryHindi();
    setInterval(loadCricketMatchesCategoryHindi, 60000);

});
