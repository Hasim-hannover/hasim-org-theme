<?php
/**
 * Template Name: Front Page
 * Description: Digital Publication / Journal – Startseite im NZZ/NYT-Stil.
 *
 * @package Hasimuener_Journal
 */

get_header(); ?>

<main id="journal-front" class="journal-front" role="main">

    <!-- 1. THE MASTHEAD -->
    <section class="masthead" aria-label="Masthead">
        <div class="masthead__inner">
            <h1 class="masthead__headline">Digital Sovereignty.</h1>
            <p class="masthead__subline">Architecture and AI-Strategy for the post-hourly era.</p>
            <a href="/manifesto" class="masthead__link">Read the manifesto &rarr;</a>
        </div>
    </section>

    <hr class="journal-rule" aria-hidden="true">

    <!-- 2. THE LEAD ARTICLE (Editorial) -->
    <article class="lead-editorial" aria-label="Lead Editorial">
        <header class="lead-editorial__header">
            <span class="lead-editorial__kicker">Editorial</span>
            <h2 class="lead-editorial__title">The End of the Billable Hour</h2>
        </header>

        <div class="lead-editorial__columns">
            <div class="lead-editorial__col">
                <p>
                    For decades, the consulting industry has operated on a flawed premise:
                    that the value of intellectual work can be measured in time. A senior
                    architect who solves a systemic problem in forty minutes is worth less,
                    by this logic, than a junior developer who spends forty hours
                    circling it. The model rewards inefficiency. It penalises mastery.
                </p>
                <p>
                    In the age of generative AI, this contradiction becomes untenable.
                    When a well-crafted prompt produces in seconds what once took days of
                    manual labour, the hourly rate ceases to function as a unit of value.
                    What remains is the architecture — the decisions about structure,
                    sequence, and system design that no model can make autonomously.
                </p>
            </div>
            <div class="lead-editorial__col">
                <p>
                    We have therefore abandoned the hourly model entirely. In its place,
                    we offer modules: clearly scoped units of architectural value, each
                    with a defined outcome, a fixed investment, and a measurable result.
                    The client pays for the transformation, not for the time spent
                    achieving it.
                </p>
                <p>
                    This is not a pricing strategy. It is a philosophical position.
                    We believe that sovereignty — digital, operational, strategic —
                    begins with the refusal to commoditise expertise. Architecture
                    is not labour. It is judgement. And judgement has no hourly rate.
                </p>
            </div>
        </div>
    </article>

    <hr class="journal-rule" aria-hidden="true">

    <!-- 3. THE MODULES GRID (Competence Fields) -->
    <section class="competence-grid" aria-label="Competence Fields">
        <header class="competence-grid__header">
            <h2 class="competence-grid__title">Competence Fields</h2>
        </header>

        <div class="competence-grid__columns">

            <article class="competence-card">
                <span class="competence-card__number">I</span>
                <h3 class="competence-card__title">High-Performance WordPress</h3>
                <p class="competence-card__desc">
                    Technical foundation. Server-level caching, Core Web Vitals
                    optimisation, and theme architecture built for sub-second
                    load times. No page builders. No bloat. Pure engineering.
                </p>
                <span class="competence-card__label">Technical Foundation</span>
            </article>

            <article class="competence-card">
                <span class="competence-card__number">II</span>
                <h3 class="competence-card__title">Algorithmic Operations</h3>
                <p class="competence-card__desc">
                    AI and automation. From intelligent content pipelines to
                    workflow orchestration — systems that learn, adapt, and
                    reduce operational friction without human babysitting.
                </p>
                <span class="competence-card__label">AI &amp; Automation</span>
            </article>

            <article class="competence-card">
                <span class="competence-card__number">III</span>
                <h3 class="competence-card__title">The Retainer</h3>
                <p class="competence-card__desc">
                    Strategic continuity. A long-term architectural partnership
                    that ensures your digital infrastructure evolves deliberately —
                    not reactively. Quarterly reviews. Annual roadmaps.
                </p>
                <span class="competence-card__label">Strategic Continuity</span>
            </article>

        </div>
    </section>

    <hr class="journal-rule" aria-hidden="true">

    <!-- 4. COLOPHON -->
    <section class="journal-colophon" aria-label="Colophon">
        <p class="journal-colophon__text">
            Hasimuener Journal &middot; Digital Architecture &middot; Hannover
        </p>
    </section>

</main>

<?php get_footer(); ?>
